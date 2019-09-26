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
        $generatorParams->setFramework(GeneratorParams::YII_1);
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
                    if ($data = $this->getDataFromTable($db, $table)) {

                        $insertDataDto = new InsertDataDto();
                        $insertDataDto->tableName = $table;
                        $insertDataDto->data = $data;

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

    /**
     * Gets data from table.
     *
     * @param Connection $db
     * @param string $table
     * @return array|null
     */
    public function getDataFromTable(Connection $db, string $table): ?array
    {
        try {
            return $db->createCommand("SELECT * FROM `{$table}`")->queryAll();
        } catch (\yii\db\Exception $e) {
            return null;
        }
    }
}
