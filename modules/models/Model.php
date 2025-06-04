<?php
namespace Models;

class Model implements \Iterator
{
	protected const TABLE_NAME = '';
	protected const DEFAULT_ORDER = '';
	protected const RELATIONS = [];

	protected \PDO $db;
	private ?\PDOStatement $statement = null;
	private $record = false;

	public function __construct()
	{
		$this->db = \Helpers\connect_to_db();

		if (!$this->db instanceof \PDO) {
			throw new \Exception("Failed to establish PDO connection in Model constructor.");
		}
	}

	protected function run(string $sql, ?array $params = null): \PDOStatement
	{
		try {
			$stmt = $this->db->prepare($sql);
			if (is_array($params) && !empty($params)) {
				$paramIndex = 1;
				foreach ($params as $value) {
					$paramType = \PDO::PARAM_STR;
					if (is_int($value)) {
						$paramType = \PDO::PARAM_INT;
					} elseif (is_bool($value)) {
						$paramType = \PDO::PARAM_BOOL;
					} elseif (is_null($value)) {
						$paramType = \PDO::PARAM_NULL;
					}
					$stmt->bindValue($paramIndex, $value, $paramType);
					$paramIndex++;
				}
			}
			$stmt->execute();
			return $stmt;
		} catch (\PDOException $e) {
			error_log("DB Query Error: " . $e->getMessage() . " SQL: " . $sql . " Params: " . print_r($params, true));
			throw $e;
		}
	}

	protected function build_select_sql(
		string $fields = '*',
		?array $links = null,
		string $where = '',
		string $order = '',
		?int $offset = null,
		?int $limit = null,
		string $group = '',
		string $having = ''
	): string {
		$s = 'SELECT ' . $fields . ' FROM ' . static::TABLE_NAME;
		if (is_array($links) && !empty($links)) {
			foreach ($links as $ext_table) {
				if (!isset(static::RELATIONS[$ext_table])) {
					throw new \Exception("Отношение для таблицы '{$ext_table}' не определено в " . static::class . "::RELATIONS.");
				}
				$rel = static::RELATIONS[$ext_table];
				$s .= ' ' . ((key_exists('type', $rel)) ?
					$rel['type'] : 'INNER') . ' JOIN ' . $ext_table .
					' ON ' . static::TABLE_NAME . '.' .
					$rel['external'] . ' = ' . $ext_table . '.' .
					$rel['primary'];
			}
		}
		if ($where) {
			$s .= ' WHERE ' . $where;
		}
		if ($group) {
			$s .= ' GROUP BY ' . $group;
			if ($having) {
				$s .= ' HAVING ' . $having;
			}
		}
		if ($order) {
			$s .= ' ORDER BY ' . $order;
		} else {
			if (static::DEFAULT_ORDER !== '') {
				$s .= ' ORDER BY ' . static::DEFAULT_ORDER;
			}
		}
		if ($limit !== null && $offset !== null) {
			$s .= ' LIMIT ' . $offset . ', ' . $limit;
		} elseif ($limit !== null) {
			$s .= ' LIMIT ' . $limit;
		}
		$s .= ';';
		return $s;
	}

	public function current(): mixed
	{
		return $this->record;
	}
	public function key(): int
	{
		return 0;
	}
	public function next(): void
	{
		if ($this->statement) {
			$this->record = $this->statement->fetch(\PDO::FETCH_ASSOC);
		} else {
			$this->record = false;
		}
	}
	public function rewind(): void
	{
		if ($this->statement) {
			$this->statement->closeCursor();
		}
		$this->record = false;
		$this->statement = null;
	}
	public function valid(): bool
	{
		return $this->record !== false;
	}

	public function get_record(
		string $fields = '*',
		?array $links = null,
		string $where = '',
		?array $params = null
	): ?array {
		$sql = $this->build_select_sql($fields, $links, $where, '', 0, 1);
		$stmt = $this->run($sql, $params);
		return $stmt->fetch(\PDO::FETCH_ASSOC) ?: null;
	}

	// Значению ключевого поля по id
	public function get(
		$value,
		string $key_field = 'id',
		string $fields = '*',
		?array $links = null
	): ?array {
		return $this->get_record(
			$fields,
			$links,
			$key_field . ' = ?',
			[$value]
		);
	}

	// Получить запись или 404
	public function get_or_404(
		$value,
		string $key_field = 'id',
		string $fields = '*',
		?array $links = null
	): array {
		$rec = $this->get($value, $key_field, $fields, $links);
		if ($rec) {
			return $rec;
		} else {
			throw new \Page404Exception();
		}
	}

	protected function before_insert(array &$fields): void
	{
	}
	protected function before_update(array &$fields, $value, string $key_field = 'id'): void
	{
	}
	protected function before_delete($value, string $key_field = 'id'): void
	{
	}

	// INSERT
	public function insert(array $fields): int
	{
		$this->before_insert($fields);
		$s = 'INSERT INTO ' . static::TABLE_NAME;
		$s1 = ''; // Список полей
		$s2 = ''; // Список плейсхолдеров
		foreach ($fields as $n => $v) {
			if ($s1) {
				$s1 .= ', ';
				$s2 .= ', ';
			}
			$s1 .= $n;
			$s2 .= ':' . $n; // Именованные плейсхолдеры
		}
		$s .= ' (' . $s1 . ') VALUES (' . $s2 . ');';
		$this->run($s, $fields);
		return (int) $this->db->lastInsertId();
	}

	// UPDATE
	public function update(array $fields, $value, string $key_field = 'id'): void
	{
		$this->before_update($fields, $value, $key_field);
		$s = 'UPDATE ' . static::TABLE_NAME . ' SET ';
		$s1 = '';
		foreach ($fields as $n => $v) {
			if ($s1) {
				$s1 .= ', ';
			}
			$s1 .= $n . ' = :' . $n;
		}
		$s .= $s1 . ' WHERE ' . $key_field . ' = :__key;';
		$fields['__key'] = $value;
		$this->run($s, $fields);
	}

	// DELETE
	public function delete($value, string $key_field = 'id'): void
	{
		$this->before_delete($value, $key_field);
		$s = 'DELETE FROM ' . static::TABLE_NAME;
		$s .= ' WHERE ' . $key_field . ' = ?;';
		$this->run($s, [$value]);
	}

	// Все записи
	public function get_all_records(
		string $fields = '*',
		?array $links = null,
		string $where = '',
		?array $params = null,
		string $order = '',
		?int $offset = null,
		?int $limit = null,
		string $group = '',
		string $having = ''
	): array {
		$sql = $this->build_select_sql(
			$fields,
			$links,
			$where,
			$order,
			$offset,
			$limit,
			$group,
			$having
		);
		$stmt = $this->run($sql, $params);
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);
	}

	public function count_all(string $where = '', ?array $params = null): int
	{
		$linksToJoin = array_keys(static::RELATIONS);

		$sql = $this->build_select_sql('COUNT(*)', $linksToJoin, $where);
		$stmt = $this->run($sql, $params);
		return (int) $stmt->fetchColumn();
	}
}