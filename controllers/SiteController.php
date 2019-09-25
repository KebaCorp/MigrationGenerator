<?php

namespace app\controllers;

use app\models\CreateTableDto;
use app\models\GeneratorParams;
use app\models\GeneratorService;
use app\models\InsertDataDto;
use Exception;
use PDO;
use Yii\db\Connection;
use yii\web\Controller;

/**
 * Class SiteController.
 *
 * @package app\controllers
 */
class SiteController extends Controller
{
    /**
     * Generates migrations.
     *
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        // Generator params
        $generatorParams = new GeneratorParams();
        $generatorParams->setDirectory('../migrations');
        $generatorParams->setFramework(GeneratorParams::YII_2);
        $generatorParams->setDataTables([
            'menu',
        ]);

        // Database connection params
        $dbHost = 'mysql';
        $dbPort = '3306';
        $dbName = 'db_mysql_00000000_migration_generator_local';
        $dbUser = 'root';
        $dbPassword = 'rootexample';
        $dbCharset = 'utf8';

        $db = new Connection([
            'dsn'      => "mysql:host={$dbHost};port={$dbPort};dbname={$dbName}",
            'username' => $dbUser,
            'password' => $dbPassword,
            'charset'  => $dbCharset,
        ]);

        if ($tables = $db->createCommand('SHOW TABLES')->queryAll(PDO::FETCH_COLUMN)) {
            $createTableDtos = [];
            $insertDataDtos = [];

            foreach ($tables as $table) {
                $createTableDto = new CreateTableDto();
                if ($createTable = $db->createCommand("SHOW CREATE TABLE $table")->queryAll()) {

                    $createTableDto->tableName = $createTable[0]['Table'];
                    $createTableDto->createTableQuery = $createTable[0]['Create Table'];

                    $createTableDtos[] = $createTableDto;
                }

                if (in_array($table, $generatorParams->getDataTables())) {
                    if ($insertDataQuery = $this->makeRecoveryData($db, $table)) {
                        $insertDataDto = new InsertDataDto();
                        $insertDataDto->tableName = $table;
                        $insertDataDto->insertDataQuery = $insertDataQuery;

                        $insertDataDtos[] = $insertDataDto;
                    }
                }
            }

            $generatorService = new GeneratorService($generatorParams, $createTableDtos, $insertDataDtos);
            $generatorService->sort();
            $generatorService->generate();
        }

        return $this->render('index');
    }

    public function makeRecoveryData(Connection $db, string $table)
    {
        if ($data = $db->createCommand("SELECT * FROM `{$table}`")->queryAll()) {

            $insertQuery = "INSERT INTO `{$table}_1`";
            $columns = '';
            $values = [];

            $isFirstRow = true;
            foreach ($data as $datum) {

                $columnValues = [];
                $rowValues = [];
                foreach ($datum as $column => $value) {
                    if ($isFirstRow) {
                        $columnValues[] = "`$column`";
                    }
                    $rowValues[] = "'$value'";
                }

                if ($isFirstRow) {
                    $columns = implode(',', $columnValues);
                }
                $values[] = '(' . implode(',', $rowValues) . ')';

                $isFirstRow = false;
            }

            $values = implode(',', $values);

            return "{$insertQuery} ({$columns}) VALUES {$values};";
        }

        return null;
    }
}
