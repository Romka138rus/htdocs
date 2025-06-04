<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title">Вход</h2>
<section id="login-form">
	<form action="/login_post" method="post">
		<label for="username">Логин:</label>
		<input type="text" id="username" name="username"
			value="<?php echo htmlspecialchars($_SESSION['form_data']['username'] ?? ''); ?>" required><br>

		<label for="password">Пароль:</label>
		<input type="password" id="password" name="password" required><br>

		<button type="submit">Войти</button>
	</form>
</section>
<?php unset($_SESSION['form_data']); ?>

<?php require \Helpers\get_fragment_path('__footer') ?>