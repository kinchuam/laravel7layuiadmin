@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            @include('admin.system.menus._from')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','xmSelect'], function () {
            let $ = layui.jquery, form = layui.form, icon = layui.icon, xmSelect = layui.xmSelect,
                SelectCategory = xmSelect.render({
                    el: '#category',
                    name: 'parent_id',
                    model: { label: { type: 'text' } },
                    prop: { value: 'id', },
                    tips: '顶级权限',
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
                        let that = this;
                        $.get(location.href, {}, function (res) {
                            let data = res.data;
                            $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/system/system_menus/'+data.id+'/update'));
                            form.val('FormExample', {
                                "name": data.name||'',
                                "route": data.route||'',
                                "status": data.status||0,
                                "icon": data.icon||'',
                            });
                            icon.render({
                                elem: '.icon',
                                placeholder: 'select icon',
                                page: true,
                                style: 'color: #5FB878;'
                            });
                            that.GetMenus(data.parent_id||0);
                            that.GetPermission(data.permission||'');
                        })

                    },
                    GetMenus(parent_id) {
                        let that = this;
                        $.get(AppGlobalMethods.RouteUrl('admin/system/menus/list'), {parent_id}, function (res) {
                            that.SetSelect(SelectCategory, res.data, parent_id);
                        });
                    },
                    GetPermission(permission) {
                        let that = this;
                        $.get(AppGlobalMethods.RouteUrl('admin/system/permission/list'), {}, function (res) {
                            that.SetSelect(SelectPermission, res.data, permission);
                        });
                    },
                    SetSelect(SelectObj, data, value) {
                        SelectObj.update({
                            data: data,
                            autoRow: true,
                        });
                        SelectObj.setValue([value]);
                        layui.each(data, function (index, item) {
                            if (item.name === value) {
                                return true;
                            }else if (item.children.length > 0) {
                                layui.each(item.children, function (index1, item1) {
                                    if (item1.name === value) {
                                        SelectObj.changeExpandedKeys([item.name])
                                        return true;
                                    }
                                })
                            }
                        })
                    }
                };F.init();
        });
    </script>
@endsection