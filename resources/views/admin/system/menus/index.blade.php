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
                            let html = 'system.menus.create';
                            if (AppGlobalMethods.UserPermissions('system.menus.create')) {
                                html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-height="80%" data-url="'+AppGlobalMethods.RouteUrl('admin/system/system_menus/create')+'"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>';
                            }
                            html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-active="ExpandAll"><i class="layui-icon layui-icon-shrink-right"></i> 展开</a>';
                            html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-active="FoldAll"><i class="layui-icon layui-icon-spread-left"></i> 折叠</a>';
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-active="delCache"><i class="layui-icon layui-icon-delete"></i> 清除缓存</a>';
                            $("#ButtonGroup").append(html);
                        }
                    },
                    dataTable = treeTable.render({
                        elem: '#dataTable',
                        tree: {
                            iconIndex: 0,
                            isPidData: true,
                            idName: 'id',
                            pidName: 'parent_id',
                            getIcon: function(d) {
                                if (d.icon) {
                                    return '<i class="ew-tree-icon layui-icon '+d.icon+'"></i>';
                                }else if (d.children) {
                                    return '<i class="ew-tree-icon ew-tree-icon-folder"></i>';
                                } else {
                                    return '<i class="ew-tree-icon ew-tree-icon-file"></i>';
                                }
                            },
                        },
                        cols: [[
                            {field: 'name', title: '菜单名称'}
                            ,{field: 'route', title: '路由' }
                            ,{field: 'status', title: '状态', width: 120, templet: function (d) {
                                return d.status == 1?'<span class="layui-badge layui-bg-green">显示</span>':'<span class="layui-badge layui-bg-orange">隐藏</span>';
                            }}
                            ,{field: 'created_at', title: '创建时间', width:180}
                            ,{fixed: 'right', width: 200, align:'center', toolbar: function (d) {
                                let tier = 2, layIndex = d.LAY_INDEX.split('-'), addChild = false;
                                let html = '<div class="layui-btn-group">';
                                if (AppGlobalMethods.UserPermissions('system.menus.create')) {
                                    if(layIndex.length === 1 && tier > 1) {
                                        addChild = true;
                                    }else if(layIndex.length === 2 && tier > 2) {
                                        addChild = true;
                                    }
                                    if (addChild) {
                                        html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="addChild" title="添加子菜单"><i class="layui-icon layui-icon-addition"></i></a>';
                                    }
                                }
                                if (AppGlobalMethods.UserPermissions('system.menus.edit')) {
                                     html += '<a class="layui-btn layui-btn-sm" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>';
                                }
                                if (AppGlobalMethods.UserPermissions('system.menus.destroy')) {
                                    html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i></a>';
                                }
                                return html+'</div>';
                            }}
                        ]]
                        , reqData: function (data, callback) {
                            let parent_id = data ? data.id : 0;
                            $.get(AppGlobalMethods.RouteUrl('admin/system/system_menus/data'), {parent_id}, function (res) {
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
                    delCache(){
                        layer.confirm('确认清楚吗？', function(index) {
                            layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                            $.post(AppGlobalMethods.RouteUrl('admin/system/system_menus/clear_cache'), {_method:'delete'}, function (res) {
                                layer.close(index);
                                layer.msg(res.message);
                            })
                        });
                    }
                });
                treeTable.on('tool(dataTable)', function(obj){
                    let data = obj.data, layEvent = obj.event;
                    if(layEvent === 'del') {
                        layer.confirm('确认删除吗？', function(index) {
                            layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                            $.post(AppGlobalMethods.RouteUrl('admin/system/system_menus/destroy'), {_method:'delete', ids:[data.id]}, function (result) {
                                if (result.code === 0){
                                    obj.del();
                                }
                                layer.close(index);
                                layer.msg(result.message, {icon:6});
                            });
                        });
                    } else if(layEvent === 'edit'){
                        okLayer.open('编辑', AppGlobalMethods.RouteUrl('admin/system/system_menus/'+data.id+'/edit'), {height:'80%'});
                    } else if (layEvent === 'addChild'){
                        okLayer.open('添加子菜单', AppGlobalMethods.RouteUrl('admin/system/system_menus/create?parent_id='+data.id), {height:'80%'});
                    }
                });
            });
    </script>
@endsection
