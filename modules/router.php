<?php

$request_uri = trim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), '/');

// Маршруты
$routes = [
	// Главная страница
	'' => ['controller' => 'Index', 'method' => 'index'],

	// Новости
	'news' => ['controller' => 'News', 'method' => 'all_news'],
	'cats/([a-z0-9-]+)' => ['controller' => 'News', 'method' => 'all_news', 'params' => ['cat']],
	'([0-9]+)' => ['controller' => 'News', 'method' => 'single_news', 'params' => ['id']],
	'([0-9]+)/comment/add' => ['controller' => 'News', 'method' => 'add_comment', 'params' => ['id']],

	// Пользователи
	'register' => ['controller' => 'Users', 'method' => 'register_form'],
	'register_post' => ['controller' => 'Users', 'method' => 'register_post'],
	'login' => ['controller' => 'Users', 'method' => 'login_form'],
	'login_post' => ['controller' => 'Users', 'method' => 'login_post'],
	'logout' => ['controller' => 'Users', 'method' => 'logout'],
	'users/([a-zA-Z0-9_]+)' => ['controller' => 'Users', 'method' => 'profile', 'params' => ['username']],
	'users/([a-zA-Z0-9_]+)/account/edit' => ['controller' => 'Users', 'method' => 'edit_account', 'params' => ['username']],
	'users/([a-zA-Z0-9_]+)/account/editpassword' => ['controller' => 'Users', 'method' => 'edit_password', 'params' => ['username']],
	'users/([a-zA-Z0-9_]+)/account/delete' => ['controller' => 'Users', 'method' => 'delete_account', 'params' => ['username']],

	// Администрирование
	'admin/news/add' => ['controller' => 'Admin', 'method' => 'add_news_form'],
	'admin/news/add_post' => ['controller' => 'Admin', 'method' => 'add_news_post'],
	'admin/news/([0-9]+)/edit' => ['controller' => 'Admin', 'method' => 'edit_news_form', 'params' => ['id']],
	'admin/news/([0-9]+)/edit_post' => ['controller' => 'Admin', 'method' => 'edit_news_post', 'params' => ['id']],
	'admin/news/([0-9]+)/delete' => ['controller' => 'Admin', 'method' => 'delete_news', 'params' => ['id']],
];

$matched = false;
foreach ($routes as $pattern => $route) {
	$regex_pattern = '#^' . $pattern . '$#';
	if (preg_match($regex_pattern, $request_uri, $matches)) {
		$matched = true;
		$params = [];
		if (isset($route['params'])) {
			foreach ($route['params'] as $index => $param_name) {
				$params[] = $matches[$index + 1];
			}
		}

		$controller_class = '\\Controllers\\' . $route['controller'];
		$controller_method = $route['method'];

		if (!class_exists($controller_class)) {
			throw new \Exception("Контроллер не найден: " . $controller_class);
		}

		$controller = new $controller_class();

		if (!method_exists($controller, $controller_method)) {
			throw new \Exception("Метод '" . $controller_method . "' не найден в контроллере " . $controller_class);
		}

		call_user_func_array([$controller, $controller_method], $params);
		break;
	}
}

if (!$matched) {
	throw new \Page404Exception();
}

?>