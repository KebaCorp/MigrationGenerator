<?php

namespace app\models;

/**
 * Class GeneratorService.
 *
 * @package app\models
 */
class GeneratorService
{
    private $_generatorParams;
    private $_createTableDtos;
    private $_insertDataDtos;

    /**
     * GeneratorService constructor.
     *
     * @param GeneratorParams $generatorParams
     * @param CreateTableDto[] $createTableDtos
     * @param InsertDataDto[] $insertDataDtos
     */
    public function __construct(
        GeneratorParams $generatorParams,
        array $createTableDtos = [],
        array $insertDataDtos = []
    ) {
        $this->_createTableDtos = $createTableDtos;
        $this->_generatorParams = $generatorParams;
        $this->_insertDataDtos = $insertDataDtos;
    }

    public function sort()
    {
        $createTableDtos = $this->_createTableDtos;

        usort($createTableDtos, function (CreateTableDto $a, CreateTableDto $b) {
            $aForeignKeys = $a->getForeignKeys();
            $bForeignKeys = $b->getForeignKeys();

            if (in_array($a->tableName, $bForeignKeys) || (!$aForeignKeys && $bForeignKeys)) {
                return -1;
            } elseif (in_array($b->tableName, $aForeignKeys) || ($aForeignKeys && !$bForeignKeys)) {
                return 1;
            } else {
                return 0;
            }
        });

        $this->_createTableDtos = $createTableDtos;
    }

    public function generate()
    {
        $date = date('ymd_His');
        $count = 0;

        foreach ($this->_createTableDtos as $createTableDto) {
            ++$count;

            $createTableGenerator = new CreateTableGenerator(
                $createTableDto,
                $this->_generatorParams->getDirectory(),
                "{$date}_{$count}",
                $this->_generatorParams->getFileExtension()
            );

            $createTableGenerator->createFile($this->_generatorParams->getFramework());
        }

        foreach ($this->_insertDataDtos as $insertDataDto) {
            ++$count;

            $createTableGenerator = new InsertDataGenerator(
                $insertDataDto,
                $this->_generatorParams->getDirectory(),
                "{$date}_{$count}",
                $this->_generatorParams->getFileExtension()
            );

            $createTableGenerator->createFile($this->_generatorParams->getFramework());
        }
    }
}
