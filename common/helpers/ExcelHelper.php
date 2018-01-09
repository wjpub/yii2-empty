<?php
namespace common\helpers;

use moonland\phpexcel\Excel;
use Yii;

class ExcelHelper
{

    public static function getFileData($filePath)
    {
        if (!$filePath || !file_exists($filePath)) {
            return [];
        }
        $data = Excel::import($filePath, [
            'setFirstRecordAsKeys' => true, // if you want to set the keys of record column with first record, if it not set, the header with use the alphabet column on excel.
            'setIndexSheetByName' => true, // set this if your excel data with multiple worksheet, the index of array will be set with the sheet name. If this not set, the index will use numeric.
            'getOnlySheet' => 'sheet1'
        ]);
        return $data;
    }

    public static function setFileData($savePath, $fileName, $models)
    {
        Excel::export(compact('savePath', 'fileName', 'models'));
    }
}