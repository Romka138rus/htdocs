<?php
namespace Controllers;

use Services\AuthService;

abstract class Controller
{
	protected $authService;
	protected $currentUser;

	public function __construct()
	{
		$this->authService = new AuthService();
		$this->currentUser = $this->authService->get_current_user();
	}

	// Обработка шаблона
	protected function render(string $template_name, array $data = [])
	{
		global $base_path;
		extract($data);

		$__current_user = $this->currentUser;

		$template_path = $base_path . 'modules/templates/' . $template_name . '.php';

		if (!file_exists($template_path)) {
			throw new \Exception("Шаблон не найден: " . $template_path);
		}

		require $template_path;
	}
}

?>