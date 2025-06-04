<?php
namespace Controllers;

use Models\News as NewsModel;
use Models\Categories;
use Models\Comments;

class News extends Controller
{
	private NewsModel $newsModel;
	private Categories $categoryModel;
	private Comments $commentsModel;

	public function __construct()
	{
		parent::__construct();
		$this->newsModel = new NewsModel();
		$this->categoryModel = new Categories();
		$this->commentsModel = new Comments();
	}

	// Отображение с пагинацией
	public function all_news(): void
	{
		$page = (int) ($_GET['page'] ?? 1);
		if ($page < 1) {
			$page = 1;
		}
		$limit = \Settings\DEFAULT_NEWS_PER_PAGE;
		$offset = ($page - 1) * $limit;

		$categorySlug = $_GET['cat'] ?? null;
		$where = '';
		$params = null;
		$currentCategory = null;

		if ($categorySlug) {
			$currentCategory = $this->categoryModel->get_record('*', null, 'slug = ?', [$categorySlug]);
			if (!$currentCategory) {
				throw new \Page404Exception(); // Если категория не найдена 404
			}
			// Условие по категории
			$where = 'categories.slug = ?';
			$params = [$categorySlug];

		}

		// Общее кол-во новостей по категории
		$totalNewsCount = $this->newsModel->count_all($where, $params);
		$totalPages = ceil($totalNewsCount / $limit);

		$news = $this->newsModel->get_news_with_details(
			$where,
			$params,
			'uploaded_at DESC',
			$offset,
			$limit
		);

		$categories = $this->categoryModel->get_all_categories(); //

		$this->render('news_list', [
			'picts' => $news,
			'cats' => $categories,
			'current_page' => $page,
			'total_pages' => $totalPages,
			'current_category_slug' => $categorySlug,
			'site_title' => 'Все новости' . ($currentCategory ? ' :: ' . htmlspecialchars($currentCategory['name']) : '')
		]);
	}

	public function single_news(int $id)
	{
		$newsItem = $this->newsModel->get_single_news_with_details($id);

		$comments = $this->commentsModel->get_comments_for_news($id);

		$this->render('news_item', [
			'pict' => $newsItem,
			'comments' => $comments,
			'site_title' => $newsItem['title']
		]);
	}

	public function add_comment(int $news_id)
	{
		if ($_SERVER['REQUEST_METHOD'] === 'POST') {
			$comment_text = trim($_POST['comment_text'] ?? '');
			$author_name = trim($_POST['author_name'] ?? '');
			$user_id = $this->currentUser['id'] ?? null;

			if (empty($comment_text)) {
				$_SESSION['error_message'] = 'Комментарий не может быть пустым.';
				\Helpers\redirect('/' . $news_id);
			}

			if (!$user_id && empty($author_name)) {
				$_SESSION['error_message'] = 'Пожалуйста, укажите ваше имя или авторизуйтесь для комментирования.';
				\Helpers\redirect('/' . $news_id);
			}

			$fields = [
				'news_id' => $news_id,
				'comment_text' => $comment_text,
				'user_id' => $user_id
			];

			if ($user_id) {
				$full_author_name = trim(($this->currentUser['name1'] ?? '') . ' ' . ($this->currentUser['name2'] ?? ''));
				$fields['author_name'] = !empty($full_author_name) ? $full_author_name : $this->currentUser['username'];
			} else {
				$fields['author_name'] = $author_name;
			}

			$commentId = $this->commentsModel->insert($fields);

			if ($commentId) {
				$_SESSION['success_message'] = 'Комментарий успешно добавлен!';
			} else {
				$_SESSION['error_message'] = 'Ошибка при добавлении комментария.';
			}

			\Helpers\redirect('/' . $news_id);
		} else {
			throw new \Page404Exception();
		}
	}
}