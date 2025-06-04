<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title">Мой профиль</h2>
<section id="user-profile">
	<?php if (isset($user) && $user) { ?>
		<p><strong>Логин:</strong> <?php echo htmlspecialchars($user['username']) ?></p>
		<p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']) ?></p>
		<p><strong>Имя:</strong> <?php echo htmlspecialchars($user['name1']) ?></p>
		<p><strong>Фамилия:</strong> <?php echo htmlspecialchars($user['name2']) ?></p>
		<p><strong>Роль:</strong> <?php echo htmlspecialchars($user['role']) ?></p>
		<p><strong>Зарегистрирован:</strong>
			<?php echo htmlspecialchars(\Helpers\get_formatted_timestamp($user['created_at'] ?? '')) ?></p>

		<nav class="profile-nav">
			<!-- <a href="/users/<?php echo htmlspecialchars($user['username']) ?>/account/edit">Изменить данные</a> -->
			<a href="/users/<?php echo htmlspecialchars($user['username']) ?>/account/editpassword">Сменить пароль</a>
			<!-- <a href="/users/<?php echo htmlspecialchars($user['username']) ?>/account/delete"
				onclick="return confirm('Вы уверены, что хотите удалить свой аккаунт? Это действие необратимо!');">Удалить
				аккаунт</a> -->
		</nav>
	<?php } else { ?>
		<p>Данные пользователя не найдены.</p>
	<?php } ?>
</section>

<?php require \Helpers\get_fragment_path('__footer') ?>