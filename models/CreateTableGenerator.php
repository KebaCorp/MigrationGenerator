<?php

namespace app\models;

/**
 * Class CreateTableGenerator.
 *
 * @package app\models
 */
class CreateTableGenerator
{
    private $_createTableDto;

    private $_directory;
    private $_fileName;
    private $_fileExtension;

    public function __construct(CreateTableDto $createTableDto, string $directory, string $fileExtension = 'php')
    {
        $this->_createTableDto = $createTableDto;
        $this->_directory = $directory;
        $this->_fileExtension = $fileExtension;

        $time = time();
        $this->_fileName = "m{$time}_create_table__{$this->_createTableDto->tableName}";
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
        try {
            Yii::app()->db->createCommand('{$this->_createTableDto->createTableQuery}')->execute();
        } catch (\Exception \$e) {
            echo \$e->getTraceAsString();
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            Yii::app()->db->createCommand('DROP TABLE `{$this->_createTableDto->tableName}`')->execute();
        } catch (\Exception \$e) {
            echo \$e->getTraceAsString();
            return false;
        }
        
        return true;
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
        try {
            Yii::\$app->db->createCommand('{$this->_createTableDto->createTableQuery}')->execute();
        } catch (\Exception \$e) {
            echo \$e->getTraceAsString();
            return false;
        }
        
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            Yii::\$app->db->createCommand('DROP TABLE `{$this->_createTableDto->tableName}`')->execute();
        } catch (\Exception \$e) {
            echo \$e->getTraceAsString();
            return false;
        }
        
        return true;
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
