<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title"><?php echo isset($news_item) && $news_item ? 'Редактировать новость' : 'Добавить новую новость' ?>
</h2>
<section id="news-form">
	<form
		action="<?php echo isset($news_item) && $news_item ? '/admin/news/' . htmlspecialchars($news_item['id']) . '/edit_post' : '/admin/news/add_post' ?>"
		method="post" enctype="multipart/form-data">
		<label for="title">Заголовок:</label>
		<input type="text" id="title" name="title"
			value="<?php echo htmlspecialchars($_SESSION['form_data']['title'] ?? $news_item['title'] ?? ''); ?>"
			required><br>

		<label for="category_id">Категория:</label>
		<select id="category_id" name="category_id" required>
			<option value="">Выберите категорию</option>
			<?php foreach ($categories as $cat) { ?>
				<option value="<?php echo htmlspecialchars($cat['id']) ?>" <?php
					 $selected_cat_id = $_SESSION['form_data']['category_id'] ?? ($news_item['category_id'] ?? null);
					 if ($selected_cat_id == $cat['id']) {
						 echo 'selected';
					 }
					 ?>>
					<?php echo htmlspecialchars($cat['name']) ?>
				</option>
			<?php } ?>
		</select><br>

		<label for="image">Изображение:</label>
		<input type="file" id="image" name="image" accept="image/*"><br>

		<?php if (isset($news_item) && $news_item && $news_item['image_filename']) { ?>
			<p>Текущее изображение:</p>
			<img src="/<?php echo htmlspecialchars(\Settings\UPLOADS_DIR . $news_item['image_filename']) ?>"
				alt="Текущее изображение" style="max-width: 200px;"><br>
			<label>
				<input type="checkbox" name="remove_image" value="1"> Удалить текущее изображение
			</label><br>
		<?php } ?>

		<label for="content">Описание новости:</label>
		<textarea id="content" name="content" rows="10"
			required><?php echo htmlspecialchars($_SESSION['form_data']['content'] ?? $news_item['content'] ?? ''); ?></textarea><br>

		<button
			type="submit"><?php echo isset($news_item) && $news_item ? 'Сохранить изменения' : 'Добавить новость' ?></button>
	</form>
</section>
<?php unset($_SESSION['form_data']); ?>

<?php require \Helpers\get_fragment_path('__footer') ?>