<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title">Регистрация</h2>
<section id="register-form">
	<form action="/register_post" method="post">
		<label for="username">Логин:</label>
		<input type="text" id="username" name="username"
			value="<?php echo htmlspecialchars($_SESSION['form_data']['username'] ?? ''); ?>" required><br>

		<label for="email">Email:</label>
		<input type="email" id="email" name="email"
			value="<?php echo htmlspecialchars($_SESSION['form_data']['email'] ?? ''); ?>" required><br>

		<label for="password">Пароль:</label>
		<input type="password" id="password" name="password" required><br>

		<label for="password_confirm">Повторите пароль:</label>
		<input type="password" id="password_confirm" name="password_confirm" required><br>

		<label for="name1">Имя:</label>
		<input type="text" id="name1" name="name1"
			value="<?php echo htmlspecialchars($_SESSION['form_data']['name1'] ?? ''); ?>"><br>

		<label for="name2">Фамилия:</label>
		<input type="text" id="name2" name="name2"
			value="<?php echo htmlspecialchars($_SESSION['form_data']['name2'] ?? ''); ?>"><br>

		<button type="submit">Зарегистрироваться</button>
	</form>
</section>
<?php unset($_SESSION['form_data']); ?>

<?php require \Helpers\get_fragment_path('__footer') ?>