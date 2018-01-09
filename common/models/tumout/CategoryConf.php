<?php
namespace common\models\tumout;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "category_conf".
 *
 * @property integer $id
 * @property integer $parentId
 * @property integer $type
 * @property string $name
 * @property string $label
 * @property integer $status
 * @property string $creationTime
 * @property string $updateTime
 */
class CategoryConf extends ActiveRecord
{
    CONST STATUS_NEW = 0;
    CONST STATUS_DEL = 100;

    public static $STATUS_TEXT = [
        self::STATUS_NEW => '正常',
        self::STATUS_DEL => '删除',
    ];


    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'category_conf';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentId', 'type', 'status'], 'integer'],
            [['type', 'name'], 'required'],
            [['creationTime', 'updateTime'], 'safe'],
            [['name', 'label'], 'string', 'max' => 255],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'parentId' => 'Parent ID',
            'type' => 'Type',
            'name' => 'Name',
            'label' => 'Label',
            'status' => 'Status',
            'creationTime' => 'Creation Time',
            'updateTime' => 'Update Time',
        ];
    }

    public function behaviors()
    {
        return [
            [
                'class' => \common\behaviors\SaveBehavior::className(),
            ],
        ];
    }
}
