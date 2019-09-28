<?php

namespace app\models\mysql;

use app\models\CreateTableDto;
use app\models\GeneratorParams;
use app\models\InsertDataDto;

/**
 * Class MigrationGenerator.
 *
 * @package app\models\mysql
 */
class MigrationGenerator
{
    private $_generatorParams;
    private $_createTableDtos;
    private $_insertDataDtos;

    /**
     * MigrationGenerator constructor.
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
        $this->_createTableDtos = $this->_sort($createTableDtos);
        $this->_generatorParams = $generatorParams;
        $this->_insertDataDtos = $insertDataDtos;
    }

    /**
     * Generates migration files.
     *
     * @return int Number of generated files
     */
    public function generate(): int
    {
        $count = 0;

        // Creates directory if not exists
        if (!is_dir($this->_generatorParams->getDirectory())) {
            mkdir($this->_generatorParams->getDirectory(), 0755, true);
        }

        foreach ($this->_createTableDtos as $createTableDto) {
            $createTableGenerator = GeneratorCreator::createTableGenerator($this->_generatorParams, $createTableDto);
            $this->_createFile($createTableGenerator->getFileName(++$count), $createTableGenerator->getFileContent());
        }

        foreach ($this->_insertDataDtos as $insertDataDto) {
            $createTableGenerator = GeneratorCreator::createValueGenerator($this->_generatorParams, $insertDataDto);
            $this->_createFile($createTableGenerator->getFileName(++$count), $createTableGenerator->getFileContent());
        }

        return $count;
    }

    /**
     * Sorts generators.
     *
     * @param CreateTableDto[] $createTableDtos
     * @return CreateTableDto[]
     */
    private function _sort(array $createTableDtos): array
    {
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

        return $createTableDtos;
    }

    /**
     * Creates file.
     *
     * @param string $fileName
     * @param string $fileContent
     * @return bool
     */
    private function _createFile(string $fileName, string $fileContent): bool
    {
        $filename = $this->_generatorParams->getDirectory()
            . DIRECTORY_SEPARATOR
            . $fileName
            . '.'
            . $this->_generatorParams->getFileExtension();

        if ($file = fopen($filename, 'a')) {

            if (fwrite($file, $fileContent)) {
                return true;
            }

            fclose($file);
        }

        return false;
    }
}
