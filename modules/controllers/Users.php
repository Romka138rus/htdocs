<?php
namespace Controllers;

use Services\AuthService;
use Models\Users as UserModel;

class Users extends Controller
{
	private UserModel $userModel;

	public function __construct()
	{
		parent::__construct();
		$this->userModel = new UserModel();
	}

	public function register_form()
	{
		if ($this->authService->is_logged_in()) {
			\Helpers\redirect('/');
		}
		$this->render('register', [
			'site_title' => 'Регистрация'
		]);
	}

	public function register_post()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = trim($_POST['username'] ?? '');
			$email = trim($_POST['email'] ?? '');
			$password = $_POST['password'] ?? '';
			$password_confirm = $_POST['password_confirm'] ?? '';
			$name1 = trim($_POST['name1'] ?? '');
			$name2 = trim($_POST['name2'] ?? '');

			$errors = [];

			if (empty($username) || empty($email) || empty($password) || empty($password_confirm)) {
				$errors[] = 'Все обязательные поля должны быть заполнены.';
			}
			if ($password !== $password_confirm) {
				$errors[] = 'Пароли не совпадают.';
			}
			if (mb_strlen($password) < 6) {
				$errors[] = 'Пароль должен быть не менее 6 символов.';
			}
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$errors[] = 'Некорректный формат email.';
			}

			if (empty($errors)) {
				$userId = $this->authService->register([
					'username' => $username,
					'email' => $email,
					'password' => $password,
					'name1' => $name1,
					'name2' => $name2
				]);

				if ($userId) {
					$_SESSION['success_message'] = 'Вы успешно зарегистрированы!';
					$this->authService->login($username, $password);
					\Helpers\redirect('/');
				} else {
					$_SESSION['error_message'] = 'Ошибка регистрации. Возможно, логин или email уже заняты.';
					\Helpers\redirect('/register');
				}
			} else {
				$_SESSION['error_message'] = implode('<br>', $errors);
				$_SESSION['form_data'] = $_POST;
				\Helpers\redirect('/register');
			}
		} else {
			throw new \Page404Exception();
		}
	}

	public function login_form()
	{
		if ($this->authService->is_logged_in()) {
			\Helpers\redirect('/');
		}
		$this->render('login', [
			'site_title' => 'Вход'
		]);
	}

	public function login_post()
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$username = trim($_POST['username'] ?? '');
			$password = $_POST['password'] ?? '';

			if ($this->authService->login($username, $password)) {
				$_SESSION['success_message'] = 'Вы успешно авторизованы!';
				\Helpers\redirect('/');
			} else {
				$_SESSION['error_message'] = 'Неверный логин или пароль.';
				$_SESSION['form_data'] = ['username' => $username];
				\Helpers\redirect('/login');
			}
		} else {
			throw new \Page404Exception();
		}
	}

	public function logout()
	{
		$this->authService->logout();
		\Helpers\redirect('/');
	}

	public function edit_account()
	{
		if (!$this->authService->is_logged_in()) {
			throw new \Page403Exception();
		}

		$this->render('user_profile', [
			'site_title' => 'Мой профиль',
			'user' => $this->currentUser
		]);
	}

	public function edit_password()
	{
		if (!$this->authService->is_logged_in()) {
			throw new \Page403Exception();
		}
		$this->render('change_password', [
			'site_title' => 'Смена пароля'
		]);
	}

	public function delete_account()
	{
		if (!$this->authService->is_logged_in()) {
			throw new \Page403Exception();
		}
		$_SESSION['error_message'] = 'Функция удаления аккаунта не реализована.';
		\Helpers\redirect('/users/' . $this->currentUser['username'] . '/account/edit');
	}

	public function profile(string $username)
	{
		$user = $this->userModel->get_by_username($username);
		if (!$user) {
			throw new \Page404Exception();
		}
		$this->render('user_profile', [
			'user' => $user,
			'site_title' => 'Профиль пользователя ' . $username
		]);
	}
}