<?php

namespace app\utils\addons;

/**
 * 加载插件目录
 */
class Addons
{
    protected static string $addonsDir = '';

    protected static $loader = null;

    /**
     * 返回已经安装的插件
     * @authname [name]     0
     * @DateTime 2020-02-17
     * @Author   mokuyu
     * @return array [type]
     */
    public static function getInstalledAddons(): array
    {
        $list = [];
        if (is_dir(self::$addonsDir)) {
            $listDir = scandir(self::$addonsDir);
            foreach ($listDir as $key => $file) {
                if (in_array($file, ['.', '..'])) {
                    continue;
                }
                if (is_dir(self::$addonsDir . DIRECTORY_SEPARATOR . $file)) {
                    $childDir = scandir(self::$addonsDir . DIRECTORY_SEPARATOR . $file);
                    foreach ($childDir as $key => $value) {
                        if (in_array($value, ['.', '..'])) {
                            continue;
                        }
                        if (is_dir(self::$addonsDir . DIRECTORY_SEPARATOR . $file . DIRECTORY_SEPARATOR . $value)) {
                            $list[] = $file . '/' . $value;
                        }
                    }
                }
            }
        }

        return $list;
    }

    /**
     * 遍历第一级组织目录名
     * @authname [name]     0
     * @DateTime 2020-02-17
     * @Author   mokuyu
     * @param $addonsDir
     * @param $loader
     * @return void [type]
     */
    public static function load($addonsDir, $loader)
    {
        self::$addonsDir = $addonsDir;
        self::$loader    = $loader;

        if (is_dir($addonsDir)) {
            $handle = opendir($addonsDir);
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' && $file != '..') {
                    if (is_dir($addonsDir . DIRECTORY_SEPARATOR . $file)) {
                        //遍历子目录中所有插件
                        self::loadAddons($addonsDir . DIRECTORY_SEPARATOR . $file);
                    }
                }
            }
        }
    }

    /**
     * 加载组织目录中的怕有插件目录
     * @authname [name]     0
     * @DateTime 2020-02-17
     * @Author   mokuyu
     * @param    [type]   $dir [description]
     * @return void [type]
     */
    private static function loadAddons($dir)
    {
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..' && is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                //遍历子目录中所有插件
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                if (!is_file($path . '/composer.json')) {
                    continue;
                }
                $info = json_decode(file_get_contents($path . '/composer.json'), true);
                if (!$info) {
                    continue;
                }
                $psr4 = $info['autoload']['psr-4'] ?? [];
                // var_dump($psr4);
                foreach ($psr4 as $key => $value) {
                    // var_dump($)
                    $filepath = realpath($path . '/' . $value);
                    $filepath && self::$loader->addPsr4($key, $filepath, true);
                }
            }

        }
    }
}
