<?php
namespace controller\admin;

use ank\facade\App;
use ZipArchive;

/**
 * 默认控制器
 */
class Addons extends Base
{
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
        $info = [
            'name'      => 'ank/comment',
            'title'     => '插件标题',
            'descr'     => '插件描述',
            'author'    => '',
            'downloads' => 10,
            'version'   => '1.0.0',
            'homepage'  => '',
            'price'     => 0,
            'qq'        => '',
        ];
        $temAddonsPath = App::getRuntimePath() . '/addons/' . $info['name'] . '.zip';
        is_dir(dirname($temAddonsPath)) or mkdir(dirname($temAddonsPath), 777, true);
        is_file($temAddonsPath) or file_put_contents($temAddonsPath, file_get_contents('http://www.zkeli.com/comment.zip'));
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
            $info = [
                'name'      => 'ank/comment',
                'title'     => '插件标题',
                'descr'     => '插件描述',
                'author'    => '官方',
                'downloads' => 10,
                'version'   => '1.0.0',
                'homepage'  => '',
                'price'     => 100,
                'qq'        => '',
            ];
            $list = [$info, $info, $info, $info, $info, $info, $info];
            foreach ($list as $key => $value) {
                if ($value['price'] == 0) {
                    $list[$key]['price'] = '<b class="green">免费</b>';
                }
            }
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
        $this->success('插件卸载成功');
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
