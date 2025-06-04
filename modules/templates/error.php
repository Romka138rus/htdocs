<?php require \Helpers\get_fragment_path('__header') ?>

<h2 class="h2-title">Ошибка!</h2>
<section id="error-message">
	<p><?php echo htmlspecialchars($message ?? 'Произошла неизвестная ошибка.'); ?></p>
	<p><a href="/">Вернуться на главную</a></p>
</section>

<?php require \Helpers\get_fragment_path('__footer') ?>