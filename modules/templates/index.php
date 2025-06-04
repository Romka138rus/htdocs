<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title">Категории</h2>
<section id="categories">
	<?php foreach ($cats as $cat) { ?>
		<h3 class="h3-title">
			<a class="categories-items" href="/news?cat=<?php echo htmlspecialchars($cat['slug']) ?>">
				<?php echo htmlspecialchars($cat['name']) ?>
			</a>
		</h3>
	<?php } ?>
</section>

<h2 class="h2-title">Последние новости</h2>
<table id="gallery">
	<tr>
		<th></th>
		<th>Заголовок</th>
		<th>Категория</th>
		<th>Опубликовано пользователем</th>
		<th>Дата публикации</th>
		<th>Комментариев</th>
	</tr>
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
				<h4 class="h4-title"><a href="/cats/<?php echo htmlspecialchars($pict['category_slug']) ?>">
						<?php echo htmlspecialchars($pict['category_name']) ?>
					</a></h4>
			</td>
			<td>
				<h4 class="h4-title"><a href="/users/<?php echo htmlspecialchars($pict['author_username']) ?>">
						<?php echo htmlspecialchars($pict['author_username']) ?>
					</a></h4>
			</td>
			<td><?php echo htmlspecialchars(\Helpers\get_formatted_timestamp($pict['uploaded_at'])) ?></td>
			<td><?php
			echo $pict['comment_count'] ?? 0;
			?></td>
		</tr>
	<?php } ?>
</table>

<?php require \Helpers\get_fragment_path('__footer') ?>