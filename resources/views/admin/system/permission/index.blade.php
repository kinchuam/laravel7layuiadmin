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
        layui.use(['index','okLayer', 'treeTable'], function () {
            let $ = layui.jquery, treeTable = layui.treeTable, okLayer = layui.okLayer, util = layui.util,
                I = {
                    init() {
                        let html = '';
                        if (AppGlobalMethods.UserPermissions('system.permission.create')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-url="'+AppGlobalMethods.RouteUrl('admin/system/permission/create')+'"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>';
                        }
                        html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-active="ExpandAll"><i class="layui-icon layui-icon-shrink-right"></i> 展开</a>';
                        html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-active="FoldAll"><i class="layui-icon layui-icon-spread-left"></i> 折叠</a>';
                        $("#ButtonGroup").append(html);
                    }
                },
                dataTable = treeTable.render({
                    elem: '#dataTable',
                    tree: {
                        iconIndex: 0,
                        isPidData: true,
                        idName: 'id',
                        pidName: 'parent_id'
                    },
                    cols: [[
                        {field: 'name', title: '权限名称'}
                        ,{field: 'display_name', title: '显示名称', templet: function (d) {
                            return '<i class="layui-icon '+d.icon+'"> '+ d.display_name +' </i>';
                        }}
                        ,{field: 'genre', title: '类型', unresize: true, width:100, align:'center', templet: function (d) {
                            if (d.genre == 2) {
                                return '<span class="layui-badge layui-bg-blue">按钮</span>';
                            }else {
                                return '<span class="layui-badge layui-bg-green">菜单</span>';
                            }
                        }}
                        ,{field: 'created_at', title: '创建时间', width:180}
                        ,{fixed: 'right', width: 200, align:'center', templet: function (d) {
                            let tier = 3, layIndex = d.LAY_INDEX.split('-');
                            let html = '<div class="layui-btn-group">';
                            if (AppGlobalMethods.UserPermissions('system.permission.create')) {
                                if((layIndex.length === 1 && tier > 1) || (layIndex.length === 2 && tier > 2) || (layIndex.length === 3 && tier > 3)) {
                                    html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="addChild" title="添加子权限"><i class="layui-icon layui-icon-addition"></i></a>';
                                }
                            }
                            if (AppGlobalMethods.UserPermissions('system.permission.edit')) {
                                html += '<a class="layui-btn layui-btn-sm" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>';
                            }
                            if (AppGlobalMethods.UserPermissions('system.permission.destroy')) {
                                html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i></a>';
                            }
                            return html+ '</div>';
                        }}
                    ]]
                    , reqData: function (data, callback) {
                        let parent_id = data ? data.id : 0;
                        $.get(AppGlobalMethods.RouteUrl('admin/system/permission/data'), {parent_id}, function (res) {
                            callback(res.data);
                        });
                    }
                });I.init();

            util.event('lay-active', {
                ExpandAll(){
                    dataTable.expandAll();
                },
                FoldAll(){
                    dataTable.foldAll();
                },
            });
            treeTable.on('tool(dataTable)', function(obj){
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del') {
                    layer.confirm('确认删除吗？', function(index){
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/system/permission/destroy'), {_method:'delete',ids:[data.id]}, function (result) {
                            if (result.code == 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                } else if(layEvent === 'edit'){
                    okLayer.open('编辑', AppGlobalMethods.RouteUrl('admin/system/permission/'+data.id+'/edit'));
                } else if (layEvent === 'addChild') {
                    okLayer.open('添加【'+data.display_name+'】子权限', AppGlobalMethods.RouteUrl('admin/system/permission/create?parent_id='+data.id));
                }
            });
        });
    </script>
@endsection
