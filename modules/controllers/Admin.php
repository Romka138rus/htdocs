<?php
namespace Controllers;

use Models\News;
use Models\Categories;
use Services\FileUploader;

class Admin extends Controller
{
	private $newsModel;
	private $categoryModel;
	private $fileUploader;

	public function __construct()
	{
		parent::__construct();
		if (!$this->authService->is_media()) {
			throw new \Page403Exception();
		}
		$this->newsModel = new News();
		$this->categoryModel = new Categories();
		$this->fileUploader = new FileUploader();
	}

	public function add_news_form()
	{
		$categories = $this->categoryModel->get_all_records();
		$this->render('add_news', [
			'site_title' => 'Добавить новость',
			'categories' => $categories,
			'news_item' => null
		]);
	}

	// Добавление
	public function add_news_post()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$title = trim($_POST['title'] ?? '');
			$content = trim($_POST['content'] ?? '');
			$category_id = (int) ($_POST['category_id'] ?? 0);
			$user_id = $this->currentUser['id'];

			$errors = [];
			if (empty($title) || empty($content) || $category_id === 0) {
				$errors[] = 'Заголовок, описание и категория обязательны.';
			}

			$image_filename = null;
			if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
				try {
					$image_filename = $this->fileUploader->upload($_FILES['image']);
				} catch (\Exception $e) {
					$errors[] = 'Ошибка загрузки изображения: ' . $e->getMessage();
				}
			}

			if (empty($errors)) {
				$newsId = $this->newsModel->insert([
					'title' => $title,
					'content' => $content,
					'category_id' => $category_id,
					'user_id' => $user_id,
					'image_filename' => $image_filename
				]);

				if ($newsId) {
					$_SESSION['success_message'] = 'Новость успешно добавлена!';
					\Helpers\redirect('/' . $newsId);
				} else {
					$_SESSION['error_message'] = 'Ошибка при добавлении новости.';
					\Helpers\redirect('/admin/news/add');
				}
			} else {
				$_SESSION['error_message'] = implode('<br>', $errors);
				$_SESSION['form_data'] = $_POST;
				\Helpers\redirect('/admin/news/add');
			}
		} else {
			throw new \Page404Exception();
		}
	}

	// Форма для изменения
	public function edit_news_form(int $news_id)
	{
		$newsItem = $this->newsModel->get_single_news_with_details($news_id);

		if ($newsItem['user_id'] !== $this->currentUser['id'] && !$this->authService->is_admin()) {
			throw new \Page403Exception();
		}

		$categories = $this->categoryModel->get_all_records();
		$this->render('add_news', [
			'site_title' => 'Редактировать новость',
			'categories' => $categories,
			'news_item' => $newsItem
		]);
	}

	// Изменение
	public function edit_news_post(int $news_id)
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$newsItem = $this->newsModel->get_single_news_with_details($news_id);
			if (!$newsItem)
				throw new \Page404Exception();

			if ($newsItem['user_id'] !== $this->currentUser['id'] && !$this->authService->is_admin()) {
				throw new \Page403Exception();
			}

			$title = trim($_POST['title'] ?? '');
			$content = trim($_POST['content'] ?? '');
			$category_id = (int) ($_POST['category_id'] ?? 0);

			$errors = [];
			if (empty($title) || empty($content) || $category_id === 0) {
				$errors[] = 'Заголовок, описание и категория обязательны.';
			}

			$image_filename = $newsItem['image_filename'];
			if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
				try {
					$image_filename = $this->fileUploader->upload($_FILES['image']);

				} catch (\Exception $e) {
					$errors[] = 'Ошибка загрузки изображения: ' . $e->getMessage();
				}
			} else if (isset($_POST['remove_image']) && $_POST['remove_image'] === '1') {

				$image_filename = null;
			}


			if (empty($errors)) {
				$this->newsModel->update([
					'title' => $title,
					'content' => $content,
					'category_id' => $category_id,
					'image_filename' => $image_filename
				], $news_id);

				$_SESSION['success_message'] = 'Новость успешно обновлена!';
				\Helpers\redirect('/' . $news_id);
			} else {
				$_SESSION['error_message'] = implode('<br>', $errors);
				$_SESSION['form_data'] = $_POST;
				\Helpers\redirect('/admin/news/' . $news_id . '/edit');
			}
		} else {
			throw new \Page404Exception();
		}
	}

	// Удаление
	public function delete_news(int $news_id)
	{

		$newsItem = $this->newsModel->get_single_news_with_details($news_id);
		if (!$newsItem)
			throw new \Page404Exception();

		if ($newsItem['user_id'] !== $this->currentUser['id'] && !$this->authService->is_admin()) {
			throw new \Page403Exception();
		}

		$this->newsModel->delete($news_id);
		$_SESSION['success_message'] = 'Новость успешно удалена!';
		\Helpers\redirect('/');
	}
}

?>