<?php

namespace app\models;

/**
 * Class GeneratorParams.
 *
 * @package app\models
 */
class GeneratorParams
{
    /**
     * Frameworks.
     */
    const FRAMEWORK_YII_1 = 1;
    const FRAMEWORK_YII_2 = 2;

    /**
     * Databases.
     */
    const DATABASE_MYSQL = 1;

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
    private $_framework = self::FRAMEWORK_YII_2;

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
}
