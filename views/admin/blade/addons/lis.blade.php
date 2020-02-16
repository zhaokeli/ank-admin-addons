@extends("pub/base-iframe")
@section("main-area")
<div class="main-con addons" id="app">
    <!--按钮和搜索区域-->
    <div class="tool-area cl">
        <div class="btn-tool">
            <a href="javascript:;" @click="searchAddons(-1)" class="btn">全部</a>
            <a href="javascript:;" @click="searchAddons(0)" class="btn"><i class="ank-icon ank-icon-hot"></i>免费</a>
            <a href="javascript:;" @click="searchAddons(1)" class="btn"><i class="ank-icon ank-icon-money"></i>付费</a>
            {!! get_auth_btn('javascript:;','<i class="ank-icon ank-icon-user"></i>会员信息') !!}
        </div>
    </div>
    <table width="100%">
        <tr>
            <th width="200">插件名称</th>
            <th>插件描述</th>
            <th width="100">作者</th>
            <th width="100">价格</th>
            <th width="100">下载</th>
            <th width="100">版本</th>
            <th width="200">操作</th>
        </tr>
        <tr v-for="item in list" v-show="item.show">
            <td>
                <a v-show="item.homepage" style="padding-right:5px;" title="插件文档" target="_blank" v-bind:href="item.homepage"><i class="ank-icon ank-icon-home"></i></a>
                @{{ item.title }}
            </td>
            <td>@{{ item.descr }}</td>
            <td class="td-center">@{{ item.author }}</td>
            <td class="td-center">
                <template v-if="item.price>0">
                    <b class="red"><i style="color:#f00;" class="ank-icon ank-icon-money"></i>@{{ item.price }}</b>
                </template>
                <template v-else>
                    <b class="green">免费</b>
                </template>
            </td>
            <td class="td-center">@{{ item.downloads }}</td>
            <td class="td-center">@{{ item.version }}</td>
            <td>
                <a v-show="!isInstalled(item.name)" href="javascript:;" @click="install(item)" class="install"><i class="ank-icon ank-icon-cloud-download"></i>安装</a>
                <a v-show="isInstalled(item.name)" href="javascript:;" @click="unInstall(item)" class="uninstall"><i class="ank-icon ank-icon-delete"></i>卸载</a>
            </td>
        </tr>
    </table>
</div>
<script type="text/javascript">
window.addons = {
  listUrl: '{!! url("Addons/lis") !!}',
  install: '{!! url("Addons/install") !!}',
  unInstall: '{!! url("Addons/unInstall") !!}'

};
require(['addons'], function(addons) {

})
</script>
@stop
