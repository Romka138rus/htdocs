<?php require \Helpers\get_fragment_path('__header') ?>
<?php require \Helpers\get_fragment_path('__filter_form') ?>

<h2 class="h2-title">Все новости
	<?php echo htmlspecialchars($site_title) ?>
</h2>

<table id="gallery">
	<thead>
		<tr>
			<th></th>
			<th>Заголовок</th>
			<th>Категория</th>
			<th>Опубликовано пользователем</th>
			<th>Дата публикации</th>
			<th>Комментариев</th>
		</tr>
	</thead>
	<tbody>
		<?php if (!empty($picts)): ?>
			<?php foreach ($picts as $pict) { ?>
				<tr>
					<td><a href="/<?php echo htmlspecialchars($pict['id']) ?>">
							<img src="<?php echo htmlspecialchars(\Helpers\get_thumbnail($pict['image_filename'] ?? 'default.jpg')) ?>"
								alt="<?php echo htmlspecialchars($pict['title']) ?>">
						</a></td>
					<td><a href="/<?php echo htmlspecialchars($pict['id']) ?>">
							<h3 class="h3-title"><?php echo htmlspecialchars($pict['title']) ?></h3>
						</a></td>
					<td>
						<h4 class="h4-title"><a href="/cats/<?php echo htmlspecialchars($pict['category_slug'] ?? '') ?>">
								<?php echo htmlspecialchars($pict['category_name'] ?? '') ?>
							</a></h4>
					</td>
					<td>
						<h4 class="h4-title">
							<?php
							if (!empty($pict['user_id']) && isset($pict['author_username']) && $pict['author_username'] !== 'Неизвестный автор') {
								?>
								<a href="/users/<?php echo htmlspecialchars($pict['author_username']) ?>">
									<?php echo htmlspecialchars($pict['author_username']) ?>
								</a>
								<?php
							} else {
								echo htmlspecialchars($pict['author_username'] ?? 'Неизвестный автор');
							}
							?>
						</h4>
					</td>
					<td><?php echo htmlspecialchars(\Helpers\get_formatted_timestamp($pict['uploaded_at'])) ?></td>
					<td><?php echo $pict['comment_count'] ?? 0; ?></td>
				</tr>
			<?php } ?>
		<?php else: ?>
			<tr>
				<td colspan="6">
					<p style="text-align: center; padding: 20px;">Нет новостей для отображения.</p>
				</td>
			</tr>
		<?php endif; ?>
	</tbody>
</table>

<?php require \Helpers\get_fragment_path('__paginator') ?>
<?php require \Helpers\get_fragment_path('__footer') ?>