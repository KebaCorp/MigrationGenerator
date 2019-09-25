<?php

namespace app\models;

/**
 * Class CreateTableDto.
 *
 * @package app\models
 */
class CreateTableDto
{
    public $tableName;
    public $createTableQuery;

    /**
     * Returns table's foreign keys.
     * 
     * @return array
     */
    public function getForeignKeys(): array
    {
        preg_match('/REFERENCES `([a-zA-Z_]+)/i', $this->createTableQuery, $matches);

        if (!empty($matches)) {
            array_shift($matches);
        }

        return $matches;
    }
}
