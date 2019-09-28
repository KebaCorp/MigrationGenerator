<?php

namespace app\models;

/**
 * Interface GeneratorInterface.
 *
 * @package app\models
 */
interface GeneratorInterface
{
    /**
     * Returns migration's file name.
     *
     * @param string $prefix
     * @return string
     */
    public function getFileName($prefix = ''): string;

    /**
     * Returns migration's file content.
     *
     * @return string
     */
    public function getFileContent(): string;
}
