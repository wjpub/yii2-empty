<?php
$config = [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute'=> 'account/index',
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
        'accountMailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => 'smtp.mxhichina.com',
                'port' => '465',
                'encryption' => 'ssl',
                'username' => '',
                'password' => ''
            ],
            'messageConfig'=>[
                'charset'=>'UTF-8',
                'from'=>[
                ]
            ],
        ],
        'mns' => [
            'class' => 'wjpub\yii2mns\Mns',
            'accessId' => '',
            'accessKey' => '',
            'endPoint' => ''
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
            'itemTable' => 'auth_item',
            'assignmentTable' => 'auth_assignment',
            'itemChildTable' => 'auth_item_child',
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
                $response->headers->add('Access-Control-Allow-Origin', Yii::$app->request->getHeaders()->get('origin'));
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
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
