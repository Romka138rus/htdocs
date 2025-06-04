<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title">Смена пароля</h2>

<section id="change-password-form">
	<?php if (isset($error_message)): ?>
		<p class="error"><?php echo htmlspecialchars($error_message); ?></p>
	<?php endif; ?>
	<?php if (isset($success_message)): ?>
		<p class="success"><?php echo htmlspecialchars($success_message); ?></p>
	<?php endif; ?>

	<form action="/users/<?php echo htmlspecialchars($user['username']); ?>/account/editpassword" method="post">
		<label for="old_password">Текущий пароль:</label><br>
		<input type="password" id="old_password" name="old_password" required><br><br>

		<label for="new_password">Новый пароль:</label><br>
		<input type="password" id="new_password" name="new_password" required><br><br>

		<label for="confirm_password">Повторите новый пароль:</label><br>
		<input type="password" id="confirm_password" name="confirm_password" required><br><br>

		<button type="submit">Сменить пароль</button>
	</form>
</section>

<?php require \Helpers\get_fragment_path('__footer') ?>