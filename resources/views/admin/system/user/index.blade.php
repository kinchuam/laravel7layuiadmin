@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-btn-group" id="ButtonGroup"></div>
        </div>
        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','okLayer'], function () {
            let $ = layui.jquery, table = layui.table, okLayer = layui.okLayer,
                I = {
                    init() {
                        let html = '';
                        if (AppGlobalMethods.UserPermissions('system.user.destroy')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-filter="destroy" data-url="'+AppGlobalMethods.RouteUrl('admin/system/user/destroy')+'"><i class="layui-icon layui-icon-delete"></i> 删除</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('system.user.create')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-url="'+AppGlobalMethods.RouteUrl('admin/system/user/create')+'"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>';
                        }
                        $("#ButtonGroup").append(html);
                        table.render({
                            elem: '#dataTable'
                            ,url: AppGlobalMethods.RouteUrl('admin/system/user/data')
                            ,page: true
                            ,cols: [[
                                {checkbox: true,fixed: true}
                                ,{field: 'username', title: '账号'}
                                ,{field: 'display_name', title: '昵称'}
                                ,{field: 'created_at', title: '创建时间',width:200}
                                ,{field: 'updated_at', title: '更新时间',width:200}
                                ,{fixed: 'right', width: 300, align:'center', templet: function (d) {
                                        let html = '<div class="layui-btn-group">';
                                        if (AppGlobalMethods.UserPermissions('system.user.edit')) {
                                            html += '<a class="layui-btn layui-btn-sm" lay-event="edit"  title="编辑"><i class="layui-icon layui-icon-edit"></i></a>';
                                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="password"  title="设置密码"><i class="layui-icon layui-icon-password"></i></a>';
                                        }
                                        if (AppGlobalMethods.UserPermissions('system.user.role')) {
                                            html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="role" title="设置角色"><i class="layui-icon layui-icon-group"></i></a>';
                                        }
                                        if (AppGlobalMethods.UserPermissions('system.user.permission')) {
                                            html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="permission" title="设置权限"><i class="layui-icon layui-icon-set-fill"></i></a>';
                                        }
                                        if (AppGlobalMethods.UserPermissions('system.user.destroy') && d.id !== 1) {
                                            html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i></a>';
                                        }
                                        return html+'</div>';
                                    }}
                            ]]
                        })
                    }
                };I.init();

            table.on('tool(dataTable)', function(obj) {
                let data = obj.data ,layEvent = obj.event;
                if(layEvent === 'del') {
                    layer.confirm('确认删除吗？', function(index) {
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/system/user/destroy'), { _method:'delete', ids:[data.id] }, function (result) {
                            if (result.code === 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message, {icon:6});
                        });
                    });
                } else if(layEvent === 'edit') {
                    okLayer.open('更新', AppGlobalMethods.RouteUrl('admin/system/user/'+data.id+'/edit'));
                } else if(layEvent === 'password') {
                    okLayer.open('设置【'+data.display_name+'】密码', AppGlobalMethods.RouteUrl('admin/system/user/'+data.id+'/password'));
                } else if (layEvent === 'role') {
                    okLayer.open('账号【'+data.display_name+'】分配角色', AppGlobalMethods.RouteUrl('admin/system/user/'+data.id+'/role'));
                } else if (layEvent === 'permission') {
                    okLayer.open('账号 【'+data.display_name+'】分配直接权限，直接权限与角色拥有的角色权限不冲突', AppGlobalMethods.RouteUrl('admin/system/user/'+data.id+'/permission'), {height: '80%'});
                }
            });
        });
    </script>
@endsection
