<?php

namespace app\models;

/**
 * Class InsertDataGenerator.
 *
 * @package app\models
 */
class InsertDataGenerator
{
    private $_insertDataDto;

    private $_directory;
    private $_fileName;
    private $_fileExtension;

    public function __construct(
        InsertDataDto $insertDataDto,
        string $directory,
        string $prefix,
        string $fileExtension = 'php'
    ) {
        $this->_insertDataDto = $insertDataDto;
        $this->_directory = $directory;
        $this->_fileExtension = $fileExtension;
        $this->_fileName = "m{$prefix}_insert_into_table__{$this->_insertDataDto->tableName}";
    }

    public function getFileName()
    {
        return $this->_fileName;
    }

    /**
     * @return string
     */
    public function getYii1FileContent(): string
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
        \$this->insert('{$this->_insertDataDto->tableName}', [
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

/**
 * Class {$this->getFileName()}.
 */
class {$this->getFileName()} extends CDbMigration
{
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
        \$this->truncateTable('{$this->_insertDataDto->tableName}');
    }
}

MIGRATION;

        return $fileContent;
    }

    /**
     * @return string
     */
    public function getYii2FileContent(): string
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
        \$this->insert('{$this->_insertDataDto->tableName}', [
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
        \$this->truncateTable('{$this->_insertDataDto->tableName}');
    }
}

MIGRATION;

        return $fileContent;
    }

    public function createFile(int $framework)
    {
        if (!is_dir($this->_directory)) {
            mkdir($this->_directory);
        }

        $filename = $this->_directory . DIRECTORY_SEPARATOR . $this->_fileName . '.' . $this->_fileExtension;

        if ($file = fopen($filename, 'a')) {

            switch ($framework) {
                case GeneratorParams::YII_1:
                    $content = $this->getYii1FileContent();
                    break;

                case GeneratorParams::YII_2:
                    $content = $this->getYii2FileContent();
                    break;

                default:
                    $content = $this->getYii2FileContent();
                    break;
            }

            if (!fwrite($file, $content)) {
                echo "Не могу произвести запись в файл ($filename)\n";
            }

            fclose($file);
        }

    }
}
