<?php
namespace Controllers;

class Error extends Controller
{
	public function page404()
	{
		header("HTTP/1.0 404 Not Found");
		$this->render('error', ['message' => 'Страница не найдена (404)', 'site_title' => 'Ошибка 404']);
	}

	public function page403()
	{
		header("HTTP/1.0 403 Forbidden");
		$this->render('error', ['message' => 'Доступ запрещен (403)', 'site_title' => 'Ошибка 403']);
	}

	public function page503(\Throwable $e)
	{
		header("HTTP/1.0 503 Service Unavailable");
		// Логирование реальной ошибки в продакшене, не выводя пользователю
		error_log("Unhandled exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());
		$this->render('error', ['message' => 'Произошла внутренняя ошибка сервера (503). Пожалуйста, попробуйте позже.', 'site_title' => 'Ошибка 503']);
	}
}

?>