<?php

namespace app\controllers;

use app\models\ConnectionForm;
use app\models\CreateTableDto;
use app\models\GeneratorForm;
use app\models\GeneratorParams;
use app\models\InsertDataDto;
use app\models\mysql\MigrationGenerator;
use Exception;
use PDO;
use Yii;
use yii\db\Connection;
use yii\web\Controller;

/**
 * Class SiteController.
 *
 * @package app\controllers
 */
class SiteController extends Controller
{
    /**
     * Connect to database.
     *
     * @return string
     * @throws Exception
     */
    public function actionIndex()
    {
        $connectionForm = new ConnectionForm();

        if (Yii::$app->request->isPost && $connectionForm->load(Yii::$app->request->post()) && $connectionForm->validate()) {

            $generatorForm = new GeneratorForm();
            $generatorForm->attributes = $connectionForm->attributes;

            // Database connection
            $db = $generatorForm->getDbConnection();

            if ($tables = $db->createCommand('SHOW TABLES')->queryAll(PDO::FETCH_COLUMN)) {
                $associativeTables = array_combine($tables, $tables);
                $generatorForm->tables = $tables;
                $generatorForm->tablesList = $associativeTables;
                $generatorForm->dataTablesList = $associativeTables;
            }

            return $this->render('generator-form', ['model' => $generatorForm, 'connectionForm' => $connectionForm]);
        }

        return $this->render('index', ['connectionForm' => $connectionForm]);
    }

    /**
     * Generates migrations.
     *
     * @return string|\yii\web\Response
     * @throws \yii\db\Exception
     */
    public function actionGenerate()
    {
        $generatorForm = new GeneratorForm();

        if ($generatorForm->load(Yii::$app->request->post()) && $generatorForm->validate()) {

            $db = $generatorForm->getDbConnection();

            // Generator params
            $generatorParams = new GeneratorParams();
            $generatorParams->setDirectory($generatorForm->directory);
            $generatorParams->setFramework($generatorForm->framework);
            $generatorParams->setDataTables($generatorForm->dataTables);

            $createTableDtos = [];
            $insertDataDtos = [];

            foreach ($generatorForm->tables as $table) {
                if ($createTable = $db->createCommand("SHOW CREATE TABLE $table")->queryAll()) {

                    $createTableDto = new CreateTableDto();
                    $createTableDto->tableName = $createTable[0]['Table'];
                    $createTableDto->createTableQuery = $createTable[0]['Create Table'];

                    $createTableDtos[] = $createTableDto;
                }
            }

            foreach ($generatorForm->dataTables as $table) {
                if ($data = $this->_getDataFromTable($db, $table)) {

                    $insertDataDto = new InsertDataDto();
                    $insertDataDto->tableName = $table;
                    $insertDataDto->data = $data;

                    $insertDataDtos[] = $insertDataDto;
                }
            }

            $generator = new MigrationGenerator($generatorParams, $createTableDtos, $insertDataDtos);
            $generator->generate();

            return $this->redirect(['site/index']);
        }

        return $this->render('index', ['generatorForm' => $generatorForm]);
    }

    /**
     * Gets data from table.
     *
     * @param Connection $db
     * @param string $table
     * @return array|null
     */
    private function _getDataFromTable(Connection $db, string $table): ?array
    {
        try {
            return $db->createCommand("SELECT * FROM `{$table}`")->queryAll();
        } catch (\yii\db\Exception $e) {
            return null;
        }
    }
}
