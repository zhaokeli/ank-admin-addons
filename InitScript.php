<?php
namespace ank\adminaddons;

/**
 * 初始化脚本
 */
class InitScript extends \utils\admin\InitScript
{
    private $data = [
        'pid'        => 0,
        'url'        => 'Addons/lis',
        'title'      => '插件管理',
        'icon_class' => 'ank-icon ank-icon-plugin',
    ];

    public function install(): bool
    {
        $this->addMenu($this->data);

        return true;
    }

    public function unInstall(): bool
    {
        $this->deleteMenu($this->data);

        return true;
    }

    public function update(): bool
    {
        return true;
    }
}
