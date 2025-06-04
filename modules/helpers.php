<?php
namespace Helpers;

function connect_to_db()
{
	$host = \Settings\DB_HOST;
	$db = \Settings\DB_NAME;
	$user = \Settings\DB_USERNAME;
	$pass = \Settings\DB_PASSWORD;

	$charset = 'utf8mb4';

	// DSN (Data Source Name)
	$dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
	$options = [
		\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
		\PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
		\PDO::ATTR_EMULATE_PREPARES => false,
		\PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES {$charset} COLLATE {$charset}_unicode_ci"
	];

	try {
		$pdo = new \PDO($dsn, $user, $pass, $options);
		return $pdo;
	} catch (\PDOException $e) {
		error_log("Ошибка подключения к БД: " . $e->getMessage());
		throw new \Exception("Ошибка подключения к базе данных.");
	}
}

// Шаблон
function get_fragment_path(string $fragment_name): string
{
	global $base_path;
	return $base_path . 'modules/templates/' . $fragment_name . '.inc.php';
}

// GET для URL
function get_GET_params(array $allowed_params = [], array $additional_params = []): string
{
	$params = [];
	foreach ($allowed_params as $param_name) {
		if (isset($_GET[$param_name]) && $_GET[$param_name] !== '') {
			$params[$param_name] = $_GET[$param_name];
		}
	}
	$params = array_merge($params, $additional_params);

	if (empty($params)) {
		return '';
	}
	return '?' . http_build_query($params);
}

// Получения URL миниатюры
function get_thumbnail(string $filename): string
{
	// Проверка миниатюры
	if (file_exists(\Settings\THUMBNAILS_DIR . $filename)) {
		return '/' . \Settings\THUMBNAILS_DIR . $filename;
	}
	// Заглушка
	return '/' . \Settings\UPLOADS_DIR . $filename;
}

// Даты и времени
function get_formatted_timestamp(string $timestamp_str): string
{
	try {
		$date = new \DateTime($timestamp_str);
		return $date->format('d.m.Y');
	} catch (\Exception $e) {
		return 'Неизвестная дата';
	}
}

function redirect(string $url)
{
	header("Location: " . $url);
	exit();
}