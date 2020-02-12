<?php
namespace utils\addons;

/**
 * 加载插件目录
 */
class Addons
{
    protected static $addonsDir = '';

    protected static $loader = null;

    public static function getInstalledAddons()
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

    private static function loadAddons($dir)
    {
        $handle = opendir($dir);
        while (false !== ($file = readdir($handle))) {
            if ($file != '.' && $file != '..') {
                if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                    //遍历子目录中所有插件
                    $path = $dir . DIRECTORY_SEPARATOR . $file;
                    $info = json_decode(file_get_contents($path . '/composer.json'), true);

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
}
