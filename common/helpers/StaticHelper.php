<?php
namespace common\helpers;

use common\models\Evaluation;
use common\models\Task;
use Yii;
use yii\helpers\ArrayHelper;

class StaticHelper
{
    public static $startDateArr = [
        '1week' => '-1 week',
        '2week' => '-2 week',
        '1month' => '-1 month',
        '3month' => '-3 month',
        'all' => 'all',
    ];

    public static function getStartDate($str)
    {
        $startDate = ArrayHelper::getValue(self::$startDateArr, $str, '-1 week');
        return $startDate == 'all' ? '' : date('Y-m-d H:i:s', strtotime($startDate));
    }

    public static function getCasePaths($basePath)
    {
        $paths = [];
        $dirnames = scandir($basePath);
        foreach ($dirnames as $k => $name) {
            if (!in_array($name, ['.', '..'])) {
                $dirnames[$k] = $basePath. DIRECTORY_SEPARATOR .$name;
            } else {
                unset($dirnames[$k]);
            }
        }
        while(count($dirnames)) {
            $news = [];
            foreach ($dirnames as $name) {
                if (is_dir($name)) {
                    $files = scandir($name);
                    foreach ($files as $filename) {
                        $filePath = $name .DIRECTORY_SEPARATOR. $filename;
                        if (in_array($filename, ['.', '..'])) {
                            continue;
                        } elseif (is_dir($filePath)) {
                            $news[] = $filePath;
                        } else {
                            if (self::getFileExt($filePath) == 'png') {
                                $paths[$name] = 1;
                            }
                        }
                    }
                } else if (self::getFileExt($name) == 'png') {
                    $paths[$name] = 1;
                }
            }
            $dirnames = $news;
        }
        return array_keys($paths);
    }

    public static function getFileExt($file)
    {
        $temp = explode('.', $file);
        $temp = end($temp);
        return strtolower($temp);
    }

    public static function formatAIres($json, $tool = Task::TAG_TOOL_BOX)
    {
        while($json && !is_array($json)) {
            $json = json_decode($json, true);
        }
        $evares = [];
        if (!$json || !isset($json['nodules']) || !count($json['nodules'])) {
            return json_encode($evares);
        }
        foreach ($json['nodules'] as $res) {
            if (!isset($res['slices']) || !count($res['slices']) || !isset($res['cube'])) {
                continue;
            }
            $markData = [[$res['cube'][0], $res['cube'][1]], [$res['cube'][3] - $res['cube'][0], $res['cube'][4] - $res['cube'][1]]];
            $markRange = [
                $res['cube'][0] > $res['cube'][3] ? $res['cube'][3] : $res['cube'][0],
                $res['cube'][0] > $res['cube'][3] ? $res['cube'][0] : $res['cube'][3],
                $res['cube'][1] > $res['cube'][4] ? $res['cube'][4] : $res['cube'][1],
                $res['cube'][1] > $res['cube'][4] ? $res['cube'][1] : $res['cube'][4]
            ];
            $imgData = [];
            foreach ($res['slices'] as $data) {
                $imgData[] = [
                    'imgNum' => $data['slice_index'],
                    'tagNum' => count($evares),
                    'markTool' => $tool,
                    'markData' => $tool == Task::TAG_TOOL_BOX ? $markData : $data['edge'],
                    'markRange' => $markRange,
                    'isAI' => 1,
                ];
            }
            $evares[] = [
                'markNum' => 0,
                'tagNum' => count($evares),
                'markPoint' => 0,
                'imgData' => $imgData,
            ];
        }
        return json_encode($evares);
    }

    public static function evaResultVersionUpdate($resultData, $version)
    {
        $lastVersion = Evaluation::LastVersion;
        if ($version == $lastVersion || !$version) {
            return $resultData;
        }

        foreach ($resultData as $k => &$data) {
            if ($version == 1) {
                $data = [
                    'markNum' => $data['markNum'],
                    'tagNum' => $data['tagNum'],
                    'markPoint' => $data['markPoint'],
                    'imgData' => [
                         [
                             'isAi' => isset($data['isAi']) ? $data['isAi'] : 0,
                             'imgNum' => $data['imgNum'],
                             'markTool' => $data['markTool'],
                             'markData' => isset($data['markData']) ? $data['markData'] : [],
                             'markRange' => isset($data['markRange']) ? $data['markRange'] : [],
                         ]
                    ],
                    'textData' => isset($data['textData']) ? $data['textData'] : [],
                ];
            }
        }

        $version = intval($version) + 1;
        if ($version < $lastVersion) {
            return self::evaResultVersionUpdate($resultData, $version);
        } else {
            return $resultData;
        }
    }

    public static function implodeObj($obj)
    {
        if (empty($obj)) {
            return '';
        } elseif (is_array($obj)) {
            return implode(',', $obj);
        }
        return '';

    }

    public static function filterFirstValue($data)
    {
//        var_dump($data);return;
        if (empty($data)) {
            return '';
        }
        $data = is_array($data) ? $data : explode(',', $data);
        foreach ($data as $item) {
            if ($item) {
                return $item;
            }
        }
        return '';
    }
}
