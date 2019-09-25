<?php

namespace app\models;

/**
 * Class GeneratorService.
 *
 * @package app\models
 */
class GeneratorService
{
    private $_createTableDtos;
    private $_generatorParams;

    /**
     * GeneratorService constructor.
     *
     * @param CreateTableDto[] $createTableDtos
     * @param GeneratorParams $generatorParams
     */
    public function __construct(array $createTableDtos, GeneratorParams $generatorParams)
    {
        $this->_createTableDtos = $createTableDtos;
        $this->_generatorParams = $generatorParams;
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
        foreach ($this->_createTableDtos as $createTableDto) {
            $createTableGenerator = new CreateTableGenerator(
                $createTableDto,
                $this->_generatorParams->getDirectory(),
                $this->_generatorParams->getFileExtension()
            );

            $createTableGenerator->createFile($this->_generatorParams->getFramework());
        }
    }
}
