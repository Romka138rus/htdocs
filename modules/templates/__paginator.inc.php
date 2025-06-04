<?php

if ($total_pages <= 1) {
	return;
}
?>
<section id="paginator">
	<?php
	$base_url = '/news';
	if (isset($current_category_slug) && $current_category_slug) {
		$base_url = '/cats/' . htmlspecialchars($current_category_slug);
	}
	$page_query_param = \Helpers\get_GET_params([], ['cat' => $current_category_slug]);
	if (!empty($page_query_param)) {
		$page_query_param .= '&';
	} else {
		$page_query_param .= '?';
	}
	?>

	<ul class="pagination">
		<?php if ($current_page > 1) { ?>
			<li><a
					href="<?php echo htmlspecialchars($base_url . $page_query_param . 'page=' . ($current_page - 1)) ?>">Предыдущая</a>
			</li>
		<?php } ?>

		<?php for ($i = 1; $i <= $total_pages; $i++) { ?>
			<li <?php if ($i === $current_page)
				echo 'class="active"'; ?>>
				<a href="<?php echo htmlspecialchars($base_url . $page_query_param . 'page=' . $i) ?>"><?php echo $i ?></a>
			</li>
		<?php } ?>

		<?php if ($current_page < $total_pages) { ?>
			<li><a
					href="<?php echo htmlspecialchars($base_url . $page_query_param . 'page=' . ($current_page + 1)) ?>">Следующая</a>
			</li>
		<?php } ?>
	</ul>
</section>

<style>
	.pagination {
		list-style: none;
		padding: 0;
		display: flex;
		justify-content: center;
		margin-top: 20px;
	}

	.pagination li {
		margin: 0 5px;
	}

	.pagination a {
		text-decoration: none;
		padding: 8px 12px;
		border: 1px solid #ddd;
		color: #333;
		border-radius: 4px;
	}

	.pagination li.active a {
		background-color: #007bff;
		color: white;
		border-color: #007bff;
	}

	.pagination a:hover:not(.active) {
		background-color: #f2f2f2;
	}
</style>