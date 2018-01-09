<?php
namespace common\helpers;
use Yii;

class SysMsg
{

    protected static $textTemplates = array();

    public static function register($index, $textTemplate, $code = 1)
    {
        if (isset(self::$textTemplates[$index])) {
            Yii::warning("系统消息已定义：".$index, "sysmsg");
        } else {
            self::$textTemplates[$index] = array(
                'text' => $textTemplate,
                'code' => $code,
            );
        }
    }

    public static function getErrMsg($msg = 'A_GENERAL_ERR')
    {
        $args = array();
        if (!$msg) {
            $msg = 'A_GENERAL_ERR';
        } else if (is_array($msg)) {
            $args = array_slice($msg, 1);
            $msg = $msg[0];
        }
        return SysMsg::get($msg, $args);
    }

    public static function get($index, $args = array())
    {
        if (!is_array($args)) {
            $args = array($args);
        }
        if (is_array($index)) {
            foreach ($index as $key => $val) {
                if (isset($args[$key])) {
                    $index[$key] = self::get($val, $args[$key]);
                } else {
                    $index[$key] = self::get($val);
                }
            }
            return $index;
        }
        if (isset(self::$textTemplates[$index])) {
            return call_user_func_array('sprintf', array_merge(array(self::$textTemplates[$index]["text"]), $args));
        } else {
            return $index;
        }
    }

    public static function getError($index)
    {
        $errors = current($index);
        return self::getErrData($errors[0], 1);
    }

    public static function getErrData($index = 'A_GENERAL_ERR')
    {
        $temp = $index;
        if(is_array($index)) {
            $temp = $index[0];
        }

        if($index instanceof \yii\base\Model) {
            $temp = "A_GENERAL_ERR";
            $errors = $index->getErrors();
            foreach($errors as $key => $val) {
                return self::getErrData($val);
            }
            $index = $temp;
        }

        $data = array(
            'code' => 1,
            'message' => $temp,
            'data' => [],
        );
        if(isset(self::$textTemplates[$temp])) {
            $data['code'] = self::$textTemplates[$temp]['code'];
            $data['message'] = self::getErrMsg($index);
        }
        return $data;
    }

    public static function getOkData($data = array(), $index = "")
    {
        $data = array(
            'code' => 0,
            'message' => "操作成功",
            'data' => $data,
        );
        if(isset(self::$textTemplates[$index])) {
            $data['message'] = self::get($index);
        }
        return $data;
    }

}
SysMsg::register('A_GENERAL_ERR', '操作失败');
SysMsg::register('A_PARAMS_ERR', '参数错误');
SysMsg::register('A_LOGIN_ERR', '参数错误');
SysMsg::register('A_GENERAL_OK', '操作成功');
SysMsg::register('A_ROLE_ERR', '当前用户无此权限', 2);;
SysMsg::register('A_PATH_ERR', '页面不存在', 2);
