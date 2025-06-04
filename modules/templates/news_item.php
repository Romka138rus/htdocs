<?php require \Helpers\get_fragment_path('__header') ?>

<?php if (isset($pict) && $pict) { ?>
	<section id="news-item">
		<h2 class="h2-title"><?php echo htmlspecialchars($pict['title']) ?></h2>
		<div class="news-meta">
			<p>Категория: <a
					href="/cats/<?php echo htmlspecialchars($pict['category_slug'] ?? '') ?>"><?php echo htmlspecialchars($pict['category_name'] ?? '') ?></a>
			</p>
			<p>Опубликовано пользователем:
				<?php
				// Проверка на автора
				if (!empty($pict['user_id']) && isset($pict['author_username']) && $pict['author_username'] !== 'Неизвестный автор') {
					?>
					<a href="/users/<?php echo htmlspecialchars($pict['author_username']) ?>">
						<?php
						echo htmlspecialchars(
							($pict['user_name1'] ?? '') .
							(!empty($pict['user_name1']) && !empty($pict['user_name2']) ? ' ' : '') .
							($pict['user_name2'] ?? '') ?:
							($pict['author_username'] ?? 'Неизвестный автор')
						);
						?>
					</a>
					<?php
				} else {
					echo htmlspecialchars($pict['author_username'] ?? 'Неизвестный автор');
				}
				?>
			</p>
			<p>Дата публикации: <?php echo htmlspecialchars(\Helpers\get_formatted_timestamp($pict['uploaded_at'])) ?></p>
			<?php if ($pict['image_filename']) { ?>
				<p><img src="/<?php echo htmlspecialchars(\Settings\UPLOADS_DIR . $pict['image_filename']) ?>"
						alt="<?php echo htmlspecialchars($pict['title']) ?>"></p>
			<?php } ?>
		</div>
		<div class="news-content">
			<?php echo nl2br(htmlspecialchars($pict['content'])) ?>
		</div>

		<?php if (isset($__current_user) && ($__current_user['id'] === $pict['user_id'] || ($__current_user['role'] ?? '') === 'admin')) { ?>
			<div class="news-actions">
				<a href="/admin/news/<?php echo htmlspecialchars($pict['id']) ?>/edit">Редактировать новость</a>
				<a href="/admin/news/<?php echo htmlspecialchars($pict['id']) ?>/delete"
					onclick="return confirm('Вы уверены, что хотите удалить эту новость?');">Удалить новость</a>
			</div>
		<?php } ?>

		<section id="comments">
			<h3>Комментарии (<?php echo count($comments) ?>)</h3>
			<?php if (!empty($comments)) { ?>
				<?php foreach ($comments as $comment) { ?>
					<div class="comment">
						<p class="comment-author">
							<strong>
								<?php echo htmlspecialchars($comment['user_username'] ?? 'Гость') ?>
							</strong>
							<span
								class="comment-date"><?php echo htmlspecialchars(\Helpers\get_formatted_timestamp($comment['created_at'])) ?></span>
						</p>
						<p class="comment-text"><?php echo nl2br(htmlspecialchars($comment['comment_text'])) ?></p>
					</div>
				<?php } ?>
			<?php } else { ?>
				<p>Пока нет комментариев.</p>
			<?php } ?>

			<h4>Оставить комментарий</h4>
			<?php if (isset($__current_user) && $__current_user) { ?>
				<form action="/<?php echo htmlspecialchars($pict['id']) ?>/comment/add" method="post">
					<p>Вы комментируете как: <strong><?php echo htmlspecialchars($__current_user['username']) ?></strong></p>
					<textarea name="comment_text" rows="5" placeholder="Ваш комментарий..."
						required><?php echo htmlspecialchars($_SESSION['form_data']['comment_text'] ?? '') ?></textarea><br>
					<button type="submit">Добавить комментарий</button>
				</form>
			<?php } else { ?>
				<p>Чтобы оставить комментарий, пожалуйста, <a href="/login">войдите</a> или <a
						href="/register">зарегистрируйтесь</a>.</p>
				<form action="/<?php echo htmlspecialchars($pict['id']) ?>/comment/add" method="post">
					<input type="text" name="author_name" placeholder="Ваше имя (обязательно)"
						value="<?php echo htmlspecialchars($_SESSION['form_data']['author_name'] ?? '') ?>" required><br>
					<textarea name="comment_text" rows="5" placeholder="Ваш комментарий..."
						required><?php echo htmlspecialchars($_SESSION['form_data']['comment_text'] ?? '') ?></textarea><br>
					<button type="submit">Добавить комментарий</button>
				</form>
			<?php } ?>
			<?php unset($_SESSION['form_data']); ?>
		</section>
	</section>
<?php } else { ?>
	<p>Новость не найдена.</p>
<?php } ?>
<?php require \Helpers\get_fragment_path('__footer') ?>