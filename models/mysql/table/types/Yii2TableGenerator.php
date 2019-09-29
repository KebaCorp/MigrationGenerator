<?php

namespace app\models\mysql\table\types;

use app\models\CreateTableDto;
use app\models\GeneratorInterface;

/**
 * Class Yii2TableGenerator.
 *
 * @package app\models\mysql\table\types
 */
class Yii2TableGenerator implements GeneratorInterface
{
    /**
     * Create table DTO.
     *
     * @var CreateTableDto
     */
    private $_createTableDto;

    /**
     * Yii2TableGenerator constructor.
     *
     * @param CreateTableDto $createTableDto
     */
    public function __construct(CreateTableDto $createTableDto)
    {
        $this->_createTableDto = $createTableDto;
    }

    /**
     * {@inheritdoc}
     */
    public function getFileName($prefix = ''): string
    {
        $date = date('ymd_His');
        $prefix = $prefix ? "_{$prefix}" : $prefix;

        return "m{$date}{$prefix}_create_table__{$this->_createTableDto->tableName}";
    }

    /**
     * {@inheritdoc}
     */
    public function getFileContent(): string
    {
        $createTableQuery = addcslashes($this->_createTableDto->createTableQuery, '"');

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
                private \$_tableName = '{$this->_createTableDto->tableName}';
                
                /**
                 * {@inheritdoc}
                 */
                public function safeUp()
                {
                    try {
                        Yii::\$app->db->createCommand("{$createTableQuery}")->execute();
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
                    \$this->dropTable(\$this->_tableName);
                }
            }
            
            MIGRATION;

        return $fileContent;
    }
}
