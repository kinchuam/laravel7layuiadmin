@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-btn-group" id="ButtonGroup"> </div>
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
                        if (AppGlobalMethods.UserPermissions('system.role.destroy')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-filter="destroy" data-url="'+AppGlobalMethods.RouteUrl('admin/system/role/destroy')+'"><i class="layui-icon layui-icon-delete"></i> 删除</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('system.role.create')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-url="'+AppGlobalMethods.RouteUrl('admin/system/role/create')+'"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>';
                        }
                        $("#ButtonGroup").append(html);
                    }
                };I.init();
            table.render({
                elem: '#dataTable'
                , url: AppGlobalMethods.RouteUrl('admin/system/role/data')
                , page: true
                , cols: [[
                    {checkbox: true,fixed: true}
                    ,{field: 'name', title: '名称'}
                    ,{field: 'display_name', title: '显示名称'}
                    ,{field: 'created_at', title: '创建时间'}
                    ,{field: 'updated_at', title: '更新时间'}
                    ,{fixed: 'right', width: 220, align:'center', templet: function () {
                            let html = '<div class="layui-btn-group">';
                            if (AppGlobalMethods.UserPermissions('system.role.edit')) {
                                html += '<a class="layui-btn layui-btn-sm" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>';
                            }
                            if (AppGlobalMethods.UserPermissions('system.role.permission')) {
                                html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="permission" title="设置权限"><i class="layui-icon layui-icon-set-fill"></i></a>>';
                            }
                            if (AppGlobalMethods.UserPermissions('system.role.destroy')) {
                                html += '<a class="layui-btn layui-btn-danger layui-btn-sm" title="删除" lay-event="del"><i class="layui-icon layui-icon-delete"></i></a>';
                            }
                            return html+ '</div>';
                        }}
                ]]
            })
            table.on('tool(dataTable)', function(obj) {
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del'){
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/system/role/destroy'), {_method:'delete', ids:[data.id]}, function (result) {
                            if (result.code === 0){
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                } else if(layEvent === 'edit'){
                    okLayer.open('编辑', AppGlobalMethods.RouteUrl('admin/system/role/'+data.id+'/edit'));
                } else if (layEvent === 'permission'){
                    okLayer.open('编辑【'+data.name+'】权限', AppGlobalMethods.RouteUrl('admin/system/role/'+data.id+'/permission'), {height: '80%'});
                }
            });
        })
    </script>
@endsection
