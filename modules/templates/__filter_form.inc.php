<?php ?>
<section id="filter-categories">
	<form action="/news" method="get">
		<label for="category-select">Фильтр по категории:</label>
		<select name="cat" id="category-select" onchange="this.form.submit()">
			<option value="">Все категории</option>
			<?php foreach ($cats as $cat) { ?>
				<option value="<?php echo htmlspecialchars($cat['slug']) ?>" <?php
					 if (isset($current_category_slug) && $current_category_slug === $cat['slug']) {
						 echo 'selected';
					 }
					 ?>>
					<?php echo htmlspecialchars($cat['name']) ?>
				</option>
			<?php } ?>
		</select>
		<?php if (isset($_GET['page'])) { ?>
			<input type="hidden" name="page" value="<?php echo htmlspecialchars($_GET['page']); ?>">
		<?php } ?>
	</form>
</section>