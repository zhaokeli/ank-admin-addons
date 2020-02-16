<?php
namespace controller\admin;

use ank\facade\App;
use ank\Utils;
use utils\admin\Com;
use ZipArchive;

/**
 * 默认控制器
 */
class Addons extends Base
{
    protected $apiUrl = 'http://www.zkeli.com/store.php';

    /**
     * 安装插件
     * @authname 安装插件     0
     * @DateTime 2020-02-09
     * @Author   mokuyu
     *
     * @return [type]
     */
    public function install()
    {
        $addons_id = input('addons_id', 0);
        $info      = json_decode(file_get_contents($this->apiUrl . '?id=' . $addons_id), true);
        if (!$info) {
            $this->error('获取插件信息出错');
        }
        $temAddonsPath = App::getRuntimePath() . '/addons/' . $info['name'] . '-' . $info['version'] . '.zip';
        is_dir(dirname($temAddonsPath)) or mkdir(dirname($temAddonsPath), 777, true);
        is_file($temAddonsPath) or file_put_contents($temAddonsPath, file_get_contents($info['zip']));
        $addonsInfo = $this->readZipFile($temAddonsPath, 'composer.json');
        if ($addonsInfo === false) {
            $this->error('此插件格式不正确');
        }
        $addonsInfo = json_decode($addonsInfo, true);
        //判断插件是不是已经安装
        $addonsPath = App::getAppPath() . '/addons/' . $addonsInfo['name'];
        if (is_dir($addonsPath)) {
            $this->error('此插件已经安装');
        }

        $zip     = new ZipArchive();
        $openRes = $zip->open($temAddonsPath);
        if ($openRes === true) {
            $zip->extractTo($addonsPath);
            $zip->close();
        }
        $srcDir = App::getAppPath() . '/addons/' . $addonsInfo['name'] . '/public';
        $desDir = App::getSiteRoot() . '/public';
        //复制静态资源
        if (is_dir($srcDir)) {
            Utils::copyDir($srcDir, $desDir);
        }
        $this->addonsInstall($addonsPath . '/InitScript.php', $addonsInfo['name']);
        $this->success('插件安装成功');
    }

    /**
     * [lis description]
     * @authname 插件列表     0
     * @DateTime 2020-02-09
     * @Author   mokuyu
     *
     * @return [type]
     */
    public function lis()
    {
        if ($this->isAjax()) {
            $list = json_decode(file_get_contents($this->apiUrl), true);
            // foreach ($list as $key => $value) {
            //     if ($value['price'] == 0) {
            //         $list[$key]['price'] = '<b class="green">免费</b>';
            //     }
            // }
            $this->success(['list' => $list, 'installed' => \utils\addons\Addons::getInstalledAddons()]);
        } else {
            $this->assign(['meta_title' => '插件管理']);

            return $this->fetch();
        }
    }

    /**
     * 卸载插件
     * @authname 卸载插件     0
     * @DateTime 2020-02-09
     * @Author   mokuyu
     *
     * @return [type]
     */
    public function uninstall()
    {
        $name = input('name');
        //删除静态资源,因为会误删除，所以下面就不删除啦,留给插件自己删除
        // Com::delAllFile(App::getSiteRoot() . '/public/' . $name, true);
        $temAddonsPath = App::getAppPath() . '/addons/' . $name;
        $this->addonsUnInstall($temAddonsPath . '/InitScript.php', $name);
        if (is_dir($temAddonsPath)) {
            Com::delAllFile($temAddonsPath, true);
        }

        $this->success('插件卸载成功');
    }

    /**
     * 调用插件的安装脚本
     * @authname [name]     0
     * @DateTime 2020-02-12
     * @Author   mokuyu
     *
     * @param  string   $value [description]
     * @return [type]
     */
    private function addonsInstall($filePath = '', $addonsName = '')
    {
        if (!is_file($filePath)) {
            return;
        }
        include $filePath;
        $sname = strtr($addonsName, [
            '/' => '\\',
            '-' => '',
        ]);
        $sname .= '\\InitScript';
        if (!class_exists($sname)) {
            return;
        }
        $obj = new $sname();
        $obj->install();
    }

    /**
     * 调用插件的卸载脚本
     * @authname [name]     0
     * @DateTime 2020-02-12
     * @Author   mokuyu
     *
     * @param  string   $value [description]
     * @return [type]
     */
    private function addonsUnInstall($filePath = '', $addonsName = '')
    {
        if (!is_file($filePath)) {
            return;
        }
        include $filePath;
        $sname = strtr($addonsName, [
            '/' => '\\',
            '-' => '',
        ]);
        $sname .= '\\InitScript';
        if (!class_exists($sname)) {
            return;
        }
        $obj = new $sname();
        $obj->UnInstall();
    }

    /**
     * 读取压缩包内文件
     * @authname [name]     0
     * @DateTime 2020-02-09
     * example readZipFile('E:/1.zip','composer.json')
     * @Author   mokuyu
     *
     * @param  string   $zipPath  压缩包全路径
     * @param  string   $filePath 压缩包内文件相对路径
     * @return [type]
     */
    private function readZipFile($zipPath = '', $filePath = '')
    {
        $file_protocol = 'zip://' . $zipPath . '#' . $filePath;

        $fp = fopen($file_protocol, 'rb');

        if (!$fp) {
            return false;
        }
        $content = '';
        while (!feof($fp)) {
            $content .= fread($fp, 1024);
        }

        fclose($fp);

        return $content;
    }
}
