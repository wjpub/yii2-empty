<?php
/**
 * Created by PhpStorm.
 * User: deepwise
 * Date: 2018/1/9
 * Time: 18:16
 */

namespace frontend\controllers;


use common\helpers\SysMsg;
use common\models\tumout\CategoryConf;
use yii\helpers\ArrayHelper;
use yii\web\Controller;

class CategoryControllers extends Controller
{

    public function actionGet()
    {
        $data = Yii::$app->request->post();
        $type = ArrayHelper::getValue($data, 'type', 0);
        $parentId = ArrayHelper::getValue($data, 'parentId', 0);
        $types = CategoryConf::find()
            ->where(['type' => 0])->all();
        $parents = [];
        if (!$type) {
            $list = $types;
        } elseif (!$parentId) {
            $parents = CategoryConf::find()
                ->filterWhere(['type' => $type, 'parentId' => $parentId])->all();
            if ($parentId) {
                $list = CategoryConf::find()
                    ->where(['type' => $type, 'parentId' => $parentId])->all();
            } else {
                $list = $parents;
            }
        }
        return SysMsg::getOkData(compact('type', 'parentId', 'types', 'parents', 'list'));
    }

    public function actionChangestatus()
    {
        $data = Yii::$app->request->post();
        $id = ArrayHelper::getValue($data, 'id', 0);
        $status = ArrayHelper::getValue($data, 'status', -1);
        $category = CategoryConf::findOne($id);
        if (!$category || !in_array($status, CategoryConf::$STATUS_TEXT)) {
            return SysMsg::getErrData();
        }
        $category->status = $status;
        $category->save();
        return SysMsg::getOkData();
    }

    public function actionAdd()
    {
        $obj = new CategoryConf();
        $obj->load(Yii::$app->request->post(), '');
        $obj->save(false);
        return SysMsg::getOkData();
    }

}