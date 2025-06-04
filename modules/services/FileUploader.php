<?php
namespace Services;
class FileUploader
{
	private $uploadDir;
	private $thumbnailDir;
	private $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
	private $maxFileSize = 5 * 1024 * 1024;
	private $thumbnailWidth = 200;
	private $thumbnailHeight = 150;

	public function __construct()
	{
		$this->uploadDir = \Settings\UPLOADS_DIR;
		$this->thumbnailDir = \Settings\THUMBNAILS_DIR;

		// Создание папки, если она еще не создана
		if (!\is_dir($this->uploadDir)) {
			\mkdir($this->uploadDir, 0777, true);
		}
		if (!\is_dir($this->thumbnailDir)) {
			\mkdir($this->thumbnailDir, 0777, true);
		}
	}

	// Загрузка файлов
	public function upload(array $file): ?string
	{
		if ($file['error'] !== UPLOAD_ERR_OK) {
			return null;
		}

		// Тип файла
		if (!\in_array($file['type'], $this->allowedMimeTypes)) {
			throw new \Exception("Недопустимый тип файла: " . $file['type']);
		}

		// Размер файла
		if ($file['size'] > $this->maxFileSize) {
			throw new \Exception("Файл слишком большой. Максимальный размер: " . ($this->maxFileSize / 1024 / 1024) . " MB.");
		}

		// Уникальное имя файла
		$ext = \pathinfo($file['name'], PATHINFO_EXTENSION);
		$filename = \uniqid() . '.' . $ext;
		$targetPath = $this->uploadDir . $filename;

		// Перемещение загружаемого файла
		if (!\move_uploaded_file($file['tmp_name'], $targetPath)) {
			throw new \Exception("Ошибка при перемещении загруженного файла.");
		}

		// Миниатюра
		$this->createThumbnail($targetPath, $filename);

		return $filename;
	}

	// Создания миниатюры
	private function createThumbnail(string $originalPath, string $filename)
	{
		list($width, $height, $type) = \getimagesize($originalPath);

		$newWidth = $this->thumbnailWidth;
		$newHeight = $this->thumbnailHeight;

		if ($width > $height) {
			$newHeight = \floor($height * ($newWidth / $width));
		} else {
			$newWidth = \floor($width * ($newHeight / $height));
		}

		$thumbnail = \imagecreatetruecolor($newWidth, $newHeight);

		switch ($type) {
			case IMAGETYPE_JPEG:
				$source = \imagecreatefromjpeg($originalPath);
				break;
			case IMAGETYPE_PNG:
				$source = \imagecreatefrompng($originalPath);
				\imagealphablending($thumbnail, false);
				\imagesavealpha($thumbnail, true);
				break;
			case IMAGETYPE_GIF:
				$source = \imagecreatefromgif($originalPath);
				break;
			default:
				throw new \Exception("Неподдерживаемый формат изображения для миниатюры.");
		}

		if (!$source) {
			throw new \Exception("Не удалось создать ресурс изображения из файла.");
		}

		\imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

		$thumbnailPath = $this->thumbnailDir . $filename;

		switch ($type) {
			case IMAGETYPE_JPEG:
				\imagejpeg($thumbnail, $thumbnailPath);
				break;
			case IMAGETYPE_PNG:
				\imagepng($thumbnail, $thumbnailPath);
				break;
			case IMAGETYPE_GIF:
				\imagegif($thumbnail, $thumbnailPath);
				break;
		}

		\imagedestroy($source);
		\imagedestroy($thumbnail);
	}
}

?>