<!doctype html>
<html lang="ru">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title><?php echo ((isset($site_title)) ? $site_title . ' :: ' : '') ?><?php echo \Settings\SITE_NAME ?></title>
	<link href="/style.css" rel="stylesheet" type="text/css">
	<script src="/ajaxloader.js" type="text/javascript"></script>
	<script src="/login.js" type="text/javascript"></script>
	<script src="/comments.js" type="text/javascript"></script>
	<script src="/add.js" type="text/javascript"></script>
	<script src="/delete.js" type="text/javascript"></script>
</head>

<body>
	<header>
		<h1><a href="/"><?php echo \Settings\SITE_NAME ?></a></h1>
		<nav>
			<ul>
				<li><a href="/">Главная</a></li>
				<li><a href="/news">Все новости</a></li>
				<?php if (isset($__current_user) && $__current_user) { ?>
					<li><a href="/users/<?php echo htmlspecialchars($__current_user['username']) ?>">
							<?php
							$display_name = '';
							if (!empty($__current_user['name1']) && !empty($__current_user['name2'])) {
								$display_name = htmlspecialchars($__current_user['name1'] . ' ' . $__current_user['name2']);
							} else if (!empty($__current_user['name1'])) {
								$display_name = htmlspecialchars($__current_user['name1']);
							} else {
								$display_name = htmlspecialchars($__current_user['username']);
							}
							echo $display_name;
							?>
						</a></li>
					<?php if ($__current_user['role'] === 'media' || $__current_user['role'] === 'admin') { ?>
						<li><a href="/admin/news/add">Добавить новость</a></li>
					<?php } ?>
					<li><a href="/logout">Выйти</a></li>
				<?php } else { ?>
					<li><a href="/register">Регистрация</a></li>
					<li><a href="/login">Вход</a></li>
				<?php } ?>
			</ul>
		</nav>
		<section id="messages">
			<?php if (isset($_SESSION['success_message'])): ?>
				<p class="success">
					<?php echo htmlspecialchars($_SESSION['success_message']);
					unset($_SESSION['success_message']); ?>
				</p>
			<?php endif; ?>
			<?php if (isset($_SESSION['error_message'])): ?>
				<p class="error"><?php echo htmlspecialchars($_SESSION['error_message']);
				unset($_SESSION['error_message']); ?>
				</p>
			<?php endif; ?>
		</section>
	</header>
	<main>