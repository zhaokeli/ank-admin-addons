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
        $addonsPath = App::getAppPath() . '/addons/' . $info['name'];
        if (is_dir($addonsPath)) {
            $this->error('插件目录已经存在');
        }
        $temAddonsPath = App::getRuntimePath() . '/addons/' . $info['name'] . '.zip';
        is_dir(dirname($temAddonsPath)) or mkdir(dirname($temAddonsPath), 777, true);
        is_file($temAddonsPath) or file_put_contents($temAddonsPath, file_get_contents('http://www.zkeli.com/comment.zip'));
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
        # code...
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
        # code...
    }
}
