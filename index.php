<?php

header('Content-Type: text/html; charset=UTF-8');
mb_internal_encoding("UTF-8");

$base_path = __DIR__ . '/';
require_once $base_path . 'modules/settings.php';

// !!! ВАЖНО: Подключение helpers.php должно быть ЗДЕСЬ,
// !!! ДО того, как начнут инициализироваться классы,
// !!! которые зависят от функций из helpers.php (например, Model, который вызывает connect_to_db)
require_once $base_path . 'modules/helpers.php';

// Автозагрузчик классов
spl_autoload_register(function (string $class_name) use ($base_path) {
	// Преобразуем Namespace в путь к файлу
	$path = str_replace('\\', '/', $class_name);
	$full_path = $base_path . 'modules/' . $path . '.php';

	if (file_exists($full_path)) {
		require_once $full_path;
	}
});

// Определения исключений
class Page404Exception extends Exception
{
}
class Page403Exception extends Exception
{
}

// Обработчик исключений
// set_exception_handler(function ($e) {
// 	// В продакшене лучше логировать ошибку
// 	error_log("Unhandled exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine());

// 	// Вызов контроллера ошибок
// 	$ctr = new \Controllers\Error();
// 	if ($e instanceof Page404Exception) {
// 		$ctr->page404();
// 	} elseif ($e instanceof Page403Exception) {
// 		$ctr->page403();
// 	} else {
// 		$ctr->page503($e);
// 	}
// });


// Обработчик исключений
set_exception_handler(function ($e) {
	// Выводим все детали исключения прямо в браузер для отладки
	echo "<h2 style='color: red;'>ОШИБКА: Необработанное исключение!</h2>";
	echo "<p>Сообщение: " . htmlspecialchars($e->getMessage()) . "</p>";
	echo "<p>Файл: " . htmlspecialchars($e->getFile()) . "</p>";
	echo "<p>Строка: " . htmlspecialchars($e->getLine()) . "</p>";
	echo "<h3>Трассировка стека:</h3>";
	echo "<pre>" . htmlspecialchars($e->getTraceAsString()) . "</pre>";

	// Логируем в файл, если это не 404 (в XAMPP по умолчанию error_log пишет в error.log Apache)
	if (!$e instanceof Page404Exception) {
		error_log("Unhandled exception: " . $e->getMessage() . " in " . $e->getFile() . " on line " . $e->getLine() . "\nStack trace:\n" . $e->getTraceAsString());
	}
});

// Роутер должен быть подключен в самом конце, после всех настроек и автозагрузчиков
require_once $base_path . 'modules/router.php';