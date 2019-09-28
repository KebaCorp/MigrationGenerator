<?php

namespace app\models\mysql\value\types;

use app\models\CreateTableDto;
use app\models\GeneratorInterface;
use app\models\InsertDataDto;

/**
 * Class Yii2ValueGenerator.
 *
 * @package app\models\mysql\value\types
 */
class Yii2ValueGenerator implements GeneratorInterface
{
    /**
     * Insert data DTO.
     *
     * @var CreateTableDto
     */
    private $_insertDataDto;

    /**
     * Yii2ValueGenerator constructor.
     *
     * @param InsertDataDto $insertDataDto
     */
    public function __construct(InsertDataDto $insertDataDto)
    {
        $this->_insertDataDto = $insertDataDto;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName($prefix = ''): string
    {
        $date = date('ymd_His');
        $prefix = $prefix ? "_{$prefix}" : $prefix;

        return "m{$date}{$prefix}_insert_into_table__{$this->_insertDataDto->tableName}";
    }

    /**
     * {@inheritdoc}
     */
    public function getFileContent(): string
    {
        $insertQuery = '';
        $isFirstData = true;

        foreach ($this->_insertDataDto->data as $datum) {
            if ($isFirstData) {
                $isFirstData = false;
            } else {
                $insertQuery .= "\n\n";
            }

            $insertQuery .= <<<INSERT
        \$this->insert(\$this->_tableName, [
INSERT;
            $insertQuery .= "\n";

            foreach ($datum as $name => $value) {
                $normalizedValue = is_numeric($value) ? $value : "'{$value}'";

                $insertQuery .= <<<COLUMNS
            '{$name}' => {$normalizedValue},
COLUMNS;
                $insertQuery .= "\n";
            }

            $insertQuery .= <<<INSERT
        ]);
INSERT;
        }

        $fileContent = <<<MIGRATION
<?php

use yii\db\Migration;

/**
 * Class {$this->getFileName()}.
 */
class {$this->getFileName()} extends Migration
{
    /**
     * Table name.
     *
     * @var string
     */
    private \$_tableName = '{$this->_insertDataDto->tableName}';
    
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
{$insertQuery}
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        \$this->truncateTable(\$this->_tableName);
    }
}

MIGRATION;

        return $fileContent;
    }
}
