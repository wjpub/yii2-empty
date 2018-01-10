<?php
namespace common\filters;

use common\helpers\SysMsg;
use Yii;
use yii\base\Behavior;
use yii\base\Request;
use yii\web\Controller;
use yii\web\Response;

class IdentityFilter extends Behavior
{
    public $actions = [];
    public $format = 'json';

    public function events()
    {
        return [
            Controller::EVENT_BEFORE_ACTION => 'beforeAction',
        ];
    }

    public function beforeAction($event)
    {
        if (Yii::$app->user->identity == null) {
            return $event->isValid;
        }
        $action = $event->action->id;
        if (isset($this->actions[$action])) {
            $identity = $this->actions[$action];
        } else {
            $this->exitErr('A_PATH_ERR');
        }
        $invalid = true;
        foreach ($identity as $role) {
            if (call_user_func([Yii::$app->user->identity, 'is'. ucfirst($role)]) || $role == 'user') {
                $invalid = false;
                return;
            }
        }
        if ($invalid) {
            return $this->exitErr('A_ROLE_ERR');
        }
        return $event->isValid;
    }

    protected function exitErr($err) {
        Yii::$app->response->format = Response::FORMAT_JSON;
        Yii::$app->response->data = SysMsg::getErrData($err);
        Yii::$app->response->send();
        die;
    }

}