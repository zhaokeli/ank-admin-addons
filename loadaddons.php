<?php

$addonsDir = realpath(__dir__ . '/../../../addons');
// echo $addonsDir;
// die();
// $loader = require dirname($addonsDir) . '/vendor/autoload.php';
function load_addons($dir)
{
    global $loader;
    $handle = opendir($dir);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            if (is_dir($dir . DIRECTORY_SEPARATOR . $file)) {
                //遍历子目录中所有插件
                $path = $dir . DIRECTORY_SEPARATOR . $file;
                $info = json_decode(file_get_contents($path . '/composer.json'), true);

                $psr4 = $info['autoload']['psr-4'] ?? [];
                var_dump($psr4);
                foreach ($psr4 as $key => $value) {
                    // var_dump($)
                    $loader->addPsr4($key, $path . '/' . $value, true);
                }
            }
        }
    }
}

if (is_dir($addonsDir)) {
    $handle = opendir($addonsDir);
    while (false !== ($file = readdir($handle))) {
        if ($file != '.' && $file != '..') {
            if (is_dir($addonsDir . DIRECTORY_SEPARATOR . $file)) {
                //遍历子目录中所有插件
                load_addons($addonsDir . DIRECTORY_SEPARATOR . $file);
            }
        }
    }
}
