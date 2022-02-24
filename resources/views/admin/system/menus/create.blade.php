@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.system.menus._from')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','xmSelect'], function () {
            let $ = layui.jquery, icon = layui.icon, xmSelect = layui.xmSelect,
                SelectCategory = xmSelect.render({
                    el: '#category',
                    name: 'parent_id',
                    model: { label: { type: 'text' } },
                    prop: { value: 'id', },
                    tips: '默认',
                    radio: true,
                    clickClose: true,
                    tree: { show: true, strict: false, },
                }),
                SelectPermission = xmSelect.render({
                    el: '#permission',
                    name: 'permission',
                    model: { label: { type: 'text' } },
                    prop: { name: 'display_name', value: 'name', },
                    tips: '选择权限',
                    radio: true,
                    clickClose: true,
                    tree: { show: true, strict: false, },
                }),
                F = {
                    init() {
                        $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/system/menus/store'));
                        icon.render({
                            elem: '.icon',
                            placeholder: 'select icon',
                            page: true,
                            style: 'color: #5FB878;'
                        });
                        this.GetMenus();
                        this.GetPermission();
                    },
                    GetMenus() {
                        $.get(AppGlobalMethods.RouteUrl('admin/system/menus/list'), {}, function (res) {
                            SelectCategory.update({
                                data: res.data,
                                autoRow: true,
                            });
                        });
                    },
                    GetPermission() {
                        $.get(AppGlobalMethods.RouteUrl('admin/system/permission/list'), {}, function (res) {
                            SelectPermission.update({
                                data: res.data,
                                autoRow: true,
                            });
                        });
                    }
                };F.init();
        });
    </script>
@endsection