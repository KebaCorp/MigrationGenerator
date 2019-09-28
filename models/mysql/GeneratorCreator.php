<?php

namespace app\models\mysql;

use app\models\CreateTableDto;
use app\models\GeneratorInterface;
use app\models\GeneratorParams;
use app\models\InsertDataDto;
use app\models\mysql\table\types\Yii1TableGenerator;
use app\models\mysql\table\types\Yii2TableGenerator;
use app\models\mysql\value\types\Yii1ValueGenerator;
use app\models\mysql\value\types\Yii2ValueGenerator;

/**
 * Class GeneratorCreator.
 *
 * @package app\models\mysql
 */
class GeneratorCreator
{
    /**
     * Creates new table generator.
     *
     * @param GeneratorParams $generatorParams
     * @param CreateTableDto $createTableDto
     * @return GeneratorInterface
     */
    public static function createTableGenerator(
        GeneratorParams $generatorParams,
        CreateTableDto $createTableDto
    ): GeneratorInterface {
        switch ($generatorParams->getFramework()) {

            case GeneratorParams::FRAMEWORK_YII_1:
                return new Yii1TableGenerator($createTableDto);

            case GeneratorParams::FRAMEWORK_YII_2:
            default:
                return new Yii2TableGenerator($createTableDto);
        }
    }

    /**
     * Creates new value generator.
     *
     * @param GeneratorParams $generatorParams
     * @param InsertDataDto $insertDataDto
     * @return GeneratorInterface
     */
    public static function createValueGenerator(
        GeneratorParams $generatorParams,
        InsertDataDto $insertDataDto
    ): GeneratorInterface {
        switch ($generatorParams->getFramework()) {

            case GeneratorParams::FRAMEWORK_YII_1:
                return new Yii1ValueGenerator($insertDataDto);

            case GeneratorParams::FRAMEWORK_YII_2:
            default:
                return new Yii2ValueGenerator($insertDataDto);
        }
    }
}
