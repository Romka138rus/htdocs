<?php
namespace Models;

class Users extends Model
{
	protected const TABLE_NAME = 'users';
	protected const DEFAULT_ORDER = 'created_at DESC';

	protected function before_insert(array &$fields): void
	{
		// Хэш пароля
		if (isset($fields['password'])) {
			$fields['password_hash'] = password_hash($fields['password'], PASSWORD_DEFAULT);
			unset($fields['password']); // Удаление пароля
		}
		if (!isset($fields['role'])) {
			$fields['role'] = 'user';
		}
		if (!isset($fields['created_at'])) {
			$fields['created_at'] = date('Y-m-d H:i:s');
		}
	}

	protected function before_update(array &$fields, $value, string $key_field = 'id'): void
	{
		// Хеш обновленного пароля
		if (isset($fields['password'])) {
			$fields['password_hash'] = password_hash($fields['password'], PASSWORD_DEFAULT);
			unset($fields['password']);
		}
	}

	public function get_by_username(string $username): ?array
	{
		return $this->get_record(
			'id, username, email, password_hash, name1, name2, role, created_at',
			null,
			'username = ?',
			[$username]
		);
	}

	public function get_by_email(string $email): ?array
	{
		return $this->get_record(
			'id, username, email, password_hash, name1, name2, role, created_at',
			null,
			'email = ?',
			[$email]
		);
	}

	public function verify_password(string $plain_password, string $hashed_password): bool
	{
		return password_verify($plain_password, $hashed_password);
	}
}
?>