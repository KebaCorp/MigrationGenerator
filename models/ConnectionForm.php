<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\db\Connection;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 * Class ConnectionForm.
 *
 * @package app\models
 */
class ConnectionForm extends Model
{
    public $database = GeneratorParams::DATABASE_MYSQL;
    public $dbHost = 'mysql';
    public $dbPort = '3306';
    public $dbName = 'db_mysql_00000000_migration_generator_local';
    public $dbUser = 'root';
    public $dbPassword = 'rootexample';
    public $dbCharset = 'utf8';

    public $databasesList = [];

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

        // Sets databases list
        $this->databasesList = [
            GeneratorParams::DATABASE_MYSQL => Yii::t('generator', 'MySQL'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_LOAD_BY_GUEST => [
                'database',
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
            'database',
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
                    'database',
                    'dbHost',
                    'dbPort',
                    'dbName',
                    'dbUser',
                    'dbPassword',
                    'dbCharset',
                ],
                'required'
            ],
            ['dbPassword', 'checkConnection'],
        ];
    }

    /**
     * Rule for check connection.
     *
     * @param $attribute
     */
    public function checkConnection($attribute)
    {
        try {
            (new Connection([
                'dsn'      => "mysql:host={$this->dbHost};port={$this->dbPort};dbname={$this->dbName}",
                'username' => $this->dbUser,
                'password' => $this->dbPassword,
                'charset'  => $this->dbCharset,
            ]))->open();
        } catch (Exception $e) {
            $this->addError($attribute, $e->getMessage());
        }
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'database'   => Yii::t('generator', 'Database'),
            'dbHost'     => Yii::t('generator', 'Database Host'),
            'dbPort'     => Yii::t('generator', 'Database Port'),
            'dbName'     => Yii::t('generator', 'Database Name'),
            'dbUser'     => Yii::t('generator', 'Database User'),
            'dbPassword' => Yii::t('generator', 'Database Password'),
            'dbCharset'  => Yii::t('generator', 'Database Charset'),
        ];
    }
}
