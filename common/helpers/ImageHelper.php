<?php
namespace common\helpers;

use Yii;

class ImageHelper
{

    public static function getImages($dataPath, $ids = [])
    {
        $ids = is_array($ids) ? $ids : [(int)$ids];
        $res = [];
        $images = self::images($dataPath);
        $imageInfo = getImageSize($images[0]);
        foreach ($ids as $id) {
            $imgPath = $images[$id];
            $imageData = fread(fopen($imgPath, 'r'), filesize($imgPath));
            $res[$id] = 'data:' . $imageInfo['mime'] . ';base64,' .chunk_split(base64_encode($imageData));
        }
        return $res;
    }

    public static function isPng($fileName)
    {
        $ext = substr($fileName, strrpos($fileName, '.') + 1);
        return strtolower($ext) == 'png';
    }

    public static function images($dataPath)
    {
        $imgPerfix = 'CASE_IMAGE_PATH_';
        $imgCache = Yii::$app->cache->get($imgPerfix.$dataPath);
        if (!$imgCache) {
            $imgCache = [];
            $files = scandir($dataPath);
            foreach ($files as $index => $path) {
                $imageFile = $dataPath.DIRECTORY_SEPARATOR.$files[$index];
                if (is_dir($imageFile) || !self::isPng($imageFile)) {
                    continue;
                }
                $imgCache[count($imgCache)] = $imageFile;
            }
            Yii::$app->cache->set($imgPerfix.$dataPath, json_encode($imgCache), 10 * 60);
            return $imgCache;
        }
        return json_decode($imgCache);
    }

    public static function imgConf($dataPath, $imgNums = [0])
    {
        if (!is_dir($dataPath)) {
            return false;
        }
        $files = scandir($dataPath);
        $imageCount = 0;
        $imageInfo = null;
        foreach ($files as $filename) {
            if (stripos($filename, '.png')) {
                $imageCount++;
                if (!$imageInfo) {
                    $imageInfo = getImageSize($dataPath . DIRECTORY_SEPARATOR . $filename);
                }
            }
        }
        $imgData = self::getImages($dataPath, $imgNums);
        return [
            'imgInfo' => $imageInfo,
            'imgCount' => $imageCount,
            'initImgs' => $imgData
        ];
    }

    public static function imgDcmConf($dataPath)
    {
        $dcminfo = $dataPath.DIRECTORY_SEPARATOR.'dcminfo.txt';
        if (!file_exists($dcminfo)) {
            return [];
        }
        $dcminfo = file_get_contents($dcminfo);
        $dcminfo = json_decode($dcminfo);
        return $dcminfo;
    }
}