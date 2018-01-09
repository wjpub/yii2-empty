<?php
namespace common\helpers;

use Yii;
use yii\caching\FileCache;

class CacheHelper extends FileCache
{
    const NEWTASK_DOTS = 'users_dots_newtask';
    const PUBTASK_DOTS = 'users_dots_pubtask';
    const MESSAGE_DOTS = 'users_dots_message';

    public static function getDots()
    {
        return [
            'newtask' => self::showDots(self::NEWTASK_DOTS),
            'pubtask' => self::showDots(self::PUBTASK_DOTS),
            'message' => self::showDots(self::MESSAGE_DOTS),
        ];
        die();
    }

    public static function showDots($type)
    {
        $userId = Yii::$app->user->identity->id;
        $dots = Yii::$app->cache->getValue($type);
        $dots = $dots ? json_decode($dots, true) : [];
        if (isset($dots['all']) && $dots['all'] && !isset($dots['all'][$userId])) {
            return true;
        }
        if (isset($dots['ids']) && isset($dots['ids'][$userId]) && $dots['ids'][$userId] == '1') {
            return true;
        }
        return false;
    }

    public static function readDots($type)
    {
        $userId = Yii::$app->user->identity->id;
        $dots = Yii::$app->cache->getValue($type);
        $dots = $dots ? json_decode($dots, true) : [];
        if (isset($dots['all'])) {
            $dots['all'][$userId] = 1;
        }
        if (isset($dots['ids'][$userId])) {
            unset($dots['ids'][$userId]);
        }
        Yii::$app->cache->setValue($type, json_encode($dots), 0);
    }

    public static function addDots($type, $ids)
    {
        $dots = Yii::$app->cache->getValue($type);
        $dots = $dots ? json_decode($dots, true) : [];
        if ($ids == 'all') {
            $dots['all'] = [];
        } else {
            $ids = is_array($ids) ? $ids : explode(',', $ids);
            $dots['ids'] = isset($dots['ids']) ? $dots['ids'] : [];
            foreach ($ids as $id) {
                $dots['ids'][$id] = 1;
            }
        }
        Yii::$app->cache->setValue($type, json_encode($dots), 0);
//    dots 数据结构
//        newtask
//            all []
//            ids []
//        pubtask
//            all
//            ids
//        message
//            all
//            ids
    }
}