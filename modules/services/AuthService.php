<?php
namespace Services;

use Models\Users;

class AuthService
{
	private $userModel;

	public function __construct()
	{
		$this->userModel = new Users();
		if (session_status() == PHP_SESSION_NONE) {
			session_start();
		}
	}

	// Регистрация
	public function register(array $data): ?int
	{
		if ($this->userModel->get_by_username($data['username'])) {
			return null;
		}
		if ($this->userModel->get_by_email($data['email'])) {
			return null;
		}

		$userId = $this->userModel->insert([
			'username' => $data['username'],
			'email' => $data['email'],
			'password' => $data['password'],
			'name1' => $data['name1'] ?? null,
			'name2' => $data['name2'] ?? null,
			'role' => $data['role'] ?? 'user'
		]);

		return $userId;
	}

	// Авторизация
	public function login(string $username, string $password): bool
	{
		$user = $this->userModel->get_by_username($username);

		if ($user && $this->userModel->verify_password($password, $user['password_hash'])) {
			// Успешная авторизация
			$_SESSION['user_id'] = $user['id'];
			$_SESSION['username'] = $user['username'];
			$_SESSION['role'] = $user['role'];
			$_SESSION['user_data'] = [
				'id' => $user['id'],
				'username' => $user['username'],
				'email' => $user['email'],
				'name1' => $user['name1'],
				'name2' => $user['name2'],
				'role' => $user['role']
			];
			return true;
		}
		return false;
	}

	public function logout()
	{
		session_unset();
		session_destroy();
	}

	// Проверка авторизации
	public function is_logged_in(): bool
	{
		return isset($_SESSION['user_id']);
	}

	// Получить данные текущего пользователя
	public function get_current_user(): ?array
	{
		if ($this->is_logged_in()) {
			return $_SESSION['user_data'];
		}
		return null;
	}

	// Проверка роли
	public function has_role(string $role): bool
	{
		return $this->is_logged_in() && $_SESSION['role'] === $role;
	}

	// Проверка роли СМИ
	public function is_media(): bool
	{
		return $this->has_role('media') || $this->has_role('admin');
	}

	// Проверка роли администратор
	public function is_admin(): bool
	{
		return $this->has_role('admin');
	}
}

?>