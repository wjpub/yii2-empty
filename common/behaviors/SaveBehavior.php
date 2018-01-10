<?php
namespace common\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class SaveBehavior extends Behavior
{
    public $createAttr = 'creationTime';
    public $updateAttr = 'updateTime';
    public $attrs = [];

    private $map = [];

    public function init()
    {
        if (empty($this->attrs)) {
            $this->attrs = [
                BaseActiveRecord::EVENT_BEFORE_INSERT => [
                    $this->createAttr,
                    $this->updateAttr,
                ],
                BaseActiveRecord::EVENT_BEFORE_UPDATE => [
                    $this->updateAttr,
                ]
            ];
        }

        $this->map = [
            $this->createAttr => date('Y-m-d H:i:s'),
            $this->updateAttr => date('Y-m-d H:i:s'),
        ];
    }

    //@see http://www.yiichina.com/doc/api/2.0/yii-base-behavior#events()-detail
    public function events()
    {
        return array_fill_keys(array_keys($this->attrs), 'evaluateAttributes');
    }

    public function evaluateAttributes($event)
    {
        if (isset($this->attrs[$event->name])) {
            $attrs = $this->attrs[$event->name];
            foreach ($attrs as $attr) {
                if (array_key_exists($attr, $this->owner->attributes)) {
                    $this->owner->$attr = $this->getValue($attr);
                }
            }
        }
    }

    protected function getValue($attr)
    {
        return $this->map[$attr];
    }
}
