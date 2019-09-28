<?php

namespace app\controllers;

use app\models\CreateTableDto;
use app\models\GeneratorParams;
use app\models\InsertDataDto;
use app\models\mysql\MigrationGenerator;
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
        $generatorParams->setDirectory('../migrations2');
        $generatorParams->setFramework(GeneratorParams::FRAMEWORK_YII_1);
        $generatorParams->setDataTables([
            'feedback_type',
        ]);

        // Database connection params
        $dbHost = 'mysql';
        $dbPort = '3309';
        $dbName = 'db_mysql_00000000_migration_generator_local';
        $dbUser = 'root';
        $dbPassword = 'rootexample';
        $dbCharset = 'utf8';

        $db = new Connection([
            'dsn'      => "mysql:host={$dbHost};dbname={$dbName}",
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

            $generator = new MigrationGenerator($generatorParams, $createTableDtos, $insertDataDtos);
            $generator->generate();
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
