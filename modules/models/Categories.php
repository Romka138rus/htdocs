<?php
namespace Models;

class Categories extends Model
{
	protected const TABLE_NAME = 'categories';
	protected const DEFAULT_ORDER = 'id ASC';

	public function get_all_categories(): array
	{
		return $this->get_all_records('id, name, slug', [], '', [], static::DEFAULT_ORDER);
	}
}