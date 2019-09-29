<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id'                  => env('APP_ID', 'kebacorp/migrationgenerator'),
    'basePath'            => dirname(__DIR__),
    'bootstrap'           => ['log'],
    'controllerNamespace' => 'app\commands',
    'aliases'             => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@tests' => '@app/tests',
    ],
    'components'          => [
        'cache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'log'   => [
            'targets' => [
                [
                    'class'  => yii\log\FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db'    => $db,
    ],
    'params'              => $params,
    'i18n'                => [
        'translations' => [
            '*' => [
                'class' => yii\i18n\PhpMessageSource::class,
            ],
        ],
    ],
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => yii\gii\Module::class,
    ];
}

return $config;
