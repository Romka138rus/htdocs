<?php
namespace Models;

class Comments extends Model
{
	protected const TABLE_NAME = 'comments';
	protected const DEFAULT_ORDER = 'created_at ASC';

	protected const RELATIONS = [
		'users' => [
			'type' => 'LEFT',
			'external' => 'user_id',
			'primary' => 'id'
		]
	];

	public function get_comments_for_news(int $news_id): array
	{
		$fields = 'comments.*, ' .
			'COALESCE(users.username, "Удаленный пользователь") AS user_username, ' .
			'COALESCE(users.name1, "") AS user_name1, ' .
			'COALESCE(users.name2, "") AS user_name2';
		$links = ['users'];
		$where = 'comments.news_id = ?';
		$params = [$news_id];

		return $this->get_all_records(
			$fields,
			$links,
			$where,
			$params,
			static::DEFAULT_ORDER
		);
	}

	protected function before_insert(array &$fields): void
	{
		if (!isset($fields['created_at']) || empty($fields['created_at'])) {
			$fields['created_at'] = date('Y-m-d H:i:s');
		}
	}
}