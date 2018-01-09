<?php

$config = [
    'components' => [
        'request' => [
            'cookieValidationKey' => '',
            'enableCsrfValidation' => false,
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl'=>array('accounts/login'),
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                ],
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => []
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'format' => 'json',
            'formatters' => [
                'json' => '\yii\web\JsonResponseFormatter',
            ],
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                $response->headers->add('Access-Control-Allow-Origin', Yii::$app->request->getOrigin());
                $response->headers->add('Access-Control-Allow-Credentials', "true");
                $response->headers->add('Access-Control-Allow-Headers', 'Access-Control-Allow-Origin,X-Requested-With,Access-Control-Allow-Headers,Access-Control-Allow-Methods,Content-Type,Accept');
                $response->headers->add('Access-Control-Max-Age', 86400);
                if ($response->statusCode > 400 && YII_ENV == 'prod') {
                    $response->redirect('/')->send();
                }
            },
        ],
    ],
];

if (!YII_ENV_TEST) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
    ];
}

return $config;
