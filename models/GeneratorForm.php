<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Connection;
use yii\helpers\ArrayHelper;

/**
 * Class GeneratorForm.
 *
 * @package app\models
 */
class GeneratorForm extends Model
{
    public $directory = '../migrations';
    public $framework = GeneratorParams::FRAMEWORK_YII_2;
    public $tables = [];
    public $dataTables = [];
    public $dbHost;
    public $dbPort;
    public $dbName;
    public $dbUser;
    public $dbPassword;
    public $dbCharset;

    public $frameworkList = [];
    public $tablesList = [];
    public $dataTablesList = [];

    /**
     * Database connection.
     *
     * @var Connection
     */
    private $_dbConnection;

    /**
     * Scenarios.
     */
    const SCENARIO_LOAD_BY_GUEST = 'loadByGuest';

    /**
     * {@inheritdoc}
     */
    public function init()
    {
        parent::init();

        // Sets framework list
        $this->frameworkList = [
            GeneratorParams::FRAMEWORK_YII_1 => Yii::t('generator', 'Yii 1'),
            GeneratorParams::FRAMEWORK_YII_2 => Yii::t('generator', 'Yii 2'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_LOAD_BY_GUEST => [
                'directory',
                'framework',
                'tables',
                'dataTables',
                'dbHost',
                'dbPort',
                'dbName',
                'dbUser',
                'dbPassword',
                'dbCharset',
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributes()
    {
        return [
            'directory',
            'framework',
            'tables',
            'dataTables',
            'dbHost',
            'dbPort',
            'dbName',
            'dbUser',
            'dbPassword',
            'dbCharset',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [
                [
                    'directory',
                    'framework',
                    'dbHost',
                    'dbPort',
                    'dbName',
                    'dbUser',
                    'dbPassword',
                    'dbCharset',
                ],
                'required'
            ],
            [
                ['tables', 'dataTables'],
                'each',
                'rule' => ['string'],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'directory'  => Yii::t('generator', 'Directory'),
            'framework'  => Yii::t('generator', 'Framework'),
            'tables'     => Yii::t('generator', 'Tables'),
            'dataTables' => Yii::t('generator', 'Data Tables'),
            'dbHost'     => Yii::t('generator', 'Database Host'),
            'dbPort'     => Yii::t('generator', 'Database Port'),
            'dbName'     => Yii::t('generator', 'Database Name'),
            'dbUser'     => Yii::t('generator', 'Database User'),
            'dbPassword' => Yii::t('generator', 'Database Password'),
            'dbCharset'  => Yii::t('generator', 'Database Charset'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function afterValidate()
    {
        $this->tables = $this->tables ?: [];
        $this->dataTables = $this->dataTables ?: [];

        parent::afterValidate();
    }

    /**
     * Returns database connection.
     *
     * @return Connection
     */
    public function getDbConnection(): Connection
    {
        if (!$this->_dbConnection) {
            $this->_dbConnection = new Connection([
                'dsn'      => "mysql:host={$this->dbHost};port={$this->dbPort};dbname={$this->dbName}",
                'username' => $this->dbUser,
                'password' => $this->dbPassword,
                'charset'  => $this->dbCharset,
            ]);
        }

        return $this->_dbConnection;
    }
}
