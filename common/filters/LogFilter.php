<?php
namespace common\filters;

use common\models\RequestLog;
use common\models\ResponseLog;
use Yii;
use yii\base\Behavior;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class LogFilter extends Behavior
{
    private $logId;

    public static $pathFilter = [
        'account/index' => 1,
        'account/login' => 1,
        'case/images' => 1,
        'task/dataout' => 1,
    ];

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'before',
            Controller::EVENT_AFTER_ACTION => 'after',
        ];
    }

    public function before()
    {
        $path = explode('/', Yii::$app->request->getPathInfo());
        $request = new RequestLog();
        $request->userId = Yii::$app->user->id ?: 0;
        $request->url = Yii::$app->request->getHostInfo().Yii::$app->request->url;
        $request->data = json_encode(array_merge(Yii::$app->request->post(), Yii::$app->request->get()));
        $request->controller = ArrayHelper::getValue($path, 0, '');
        $request->action = ArrayHelper::getValue($path, 1, '');
        $request->ip = Yii::$app->request->userIP;
        $request->creationTime = date('Y-m-d H:i:s');
        if (ArrayHelper::getValue(self::$pathFilter, Yii::$app->request->getPathInfo())) {
            $request->data = json_encode(['path filter']);
        } else {
            $request->data = json_encode(array_merge(Yii::$app->request->post(), Yii::$app->request->get()));
        }
        $request->save(false);
        $this->logId = $request->id;
    }

    public function after($event)
    {
        if (!$this->logId) {
            return;
        }
        $response = new ResponseLog();
        $response->id = $this->logId;
        $response->userId = Yii::$app->user->id ?: 0;
        $response->exeTime = round(Yii::getLogger()->getElapsedTime(), 4);
        $response->memUsage = (!function_exists('memory_get_usage')) ? 0 : round(memory_get_usage()/1024/1024, 2);
        $response->creationTime = date('Y-m-d H:i:s');
        if (ArrayHelper::getValue(self::$pathFilter, Yii::$app->request->getPathInfo())) {
            $response->data = json_encode(['path filter']);
        } else {
            $response->data = is_array($event->result) ? json_encode($event->result) : ''.$event->result;
            if (strlen($response->data) > 10000) {
                $response->data = '';
            }
        }
        $response->save(false);
    }
}