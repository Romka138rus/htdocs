<?php
namespace Models;

class News extends Model
{
	protected const TABLE_NAME = 'news';
	protected const DEFAULT_ORDER = 'uploaded_at DESC';

	protected const RELATIONS = [
		'categories' => [
			'type' => 'INNER',
			'external' => 'category_id',
			'primary' => 'id'
		],
		'users' => [
			'type' => 'LEFT',
			'external' => 'user_id',
			'primary' => 'id'
		]
	];

	public function get_news_with_details(
		string $where = '',
		?array $params = NULL,
		string $order = '',
		?int $offset = NULL,
		?int $limit = NULL
	): array {
		$fields = 'news.*, ' .
			'categories.name AS category_name, categories.slug AS category_slug, ' .
			'COALESCE(users.username, "Неизвестный автор") AS author_username, ' .
			'(SELECT COUNT(*) FROM comments WHERE comments.news_id = news.id) AS comment_count';

		$links = ['categories', 'users'];

		return $this->get_all_records(
			$fields,
			$links,
			$where,
			$params,
			$order ?: static::DEFAULT_ORDER,
			$offset,
			$limit
		);
	}

	public function get_single_news_with_details(int $news_id): array
	{
		$fields = 'news.*, ' .
			'categories.name AS category_name, categories.slug AS category_slug, ' .
			'COALESCE(users.username, "Неизвестный автор") AS author_username, ' .
			'COALESCE(users.name1, "") AS user_name1, ' .
			'COALESCE(users.name2, "") AS user_name2';

		$links = ['categories', 'users'];

		$newsItem = $this->get_record(
			$fields,
			$links,
			'news.id = ?',
			[$news_id]
		);

		if (!$newsItem) {
			throw new \Page404Exception();
		}
		return $newsItem;
	}
}