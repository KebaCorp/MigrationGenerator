<?php

namespace app\models;

/**
 * Class GeneratorService.
 *
 * @package app\models
 */
class GeneratorParams
{
    const YII_1 = 1;
    const YII_2 = 2;

    /**
     * @var string
     */
    private $_directory = 'migrations';

    /**
     * @var string
     */
    private $_fileExtension = 'php';

    /**
     * @var int
     */
    private $_framework = self::YII_2;

    /**
     * @var array
     */
    private $_dataTables = [];

    /**
     * @return string
     */
    public function getDirectory(): string
    {
        return $this->_directory;
    }

    /**
     * @param string $directory
     */
    public function setDirectory(string $directory): void
    {
        $this->_directory = $directory;
    }

    /**
     * @return string
     */
    public function getFileExtension(): string
    {
        return $this->_fileExtension;
    }

    /**
     * @param string $fileExtension
     */
    public function setFileExtension(string $fileExtension): void
    {
        $this->_fileExtension = $fileExtension;
    }

    /**
     * @return int
     */
    public function getFramework(): int
    {
        return $this->_framework;
    }

    /**
     * @param int $framework
     */
    public function setFramework(int $framework): void
    {
        $this->_framework = $framework;
    }

    /**
     * @return array
     */
    public function getDataTables(): array
    {
        return $this->_dataTables;
    }

    /**
     * @param array $dataTables
     */
    public function setDataTables(array $dataTables): void
    {
        $this->_dataTables = $dataTables;
    }
}
