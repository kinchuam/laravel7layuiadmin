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
        layui.use(['index'], function () {
            let $ = layui.jquery, layer = layui.layer, table = layui.table,
                I = {
                    init () {
                        let html = '';
                        if (AppGlobalMethods.UserPermissions('maintain.database.optimize')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-active="optimize"><i class="layui-icon layui-icon-set"></i> 优化表</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('maintain.database.repair')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-active="repair"><i class="layui-icon layui-icon-set"></i> 修复表</a>';
                        }
                        $("#ButtonGroup").append(html);
                    },
                    PostDeal (url) {
                        let ids = [], hasCheck = table.checkStatus('dataTable'), hasCheckData = hasCheck.data;
                        if (hasCheckData.length > 0){
                            $.each(hasCheckData,function (index,element) {
                                ids.push(element.name);
                            })
                        }
                        if (ids.length > 0) {
                            layer.confirm('确认要操作吗？', function(index) {
                                layer.msg('请求处理中...', { icon: 16, shade: 0.01, time:false });
                                $.post(url, {tables: ids}, function (result) {
                                    if (result.code == 0) {
                                        return layer.msg(result.message, { icon: 1 });
                                    }
                                    layer.msg(result.message, { icon: 5 });
                                });
                            });
                            return;
                        }
                        layer.msg("请选择需要操作的数据表");
                    }
                };I.init();
            let dataTable = table.render({
                elem: '#dataTable',
                url: AppGlobalMethods.RouteUrl("admin/maintain/database/data"),
                cols: [[
                    {checkbox: true, fixed: true}
                    ,{field: 'name', title: '数据库表'}
                    ,{field: 'rows', title: '记录数',sort: true, width: 110}
                    ,{field: 'data_length', title: '大小', width: 110, templet: function (d) {
                        return AppGlobalMethods.GetFileSize(d.data_length);
                    }}
                    ,{field: 'engine', title: '引擎', width: 90}
                    ,{field: 'collation', title: '编码', width: 180}
                    ,{field: 'create_time', title: '创建时间', width: 180}
                    ,{field: 'comment', title: '备注'}
                    ,{fixed: 'right', width: 150, align:'center', templet: function () {
                            let html = '<div class="layui-btn-group">';
                            if (AppGlobalMethods.UserPermissions('maintain.database.clear')) {
                                html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-event="clear" > 清空</a>';
                            }
                            if (AppGlobalMethods.UserPermissions('maintain.database.destroy')) {
                                html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="delete" ><i class="layui-icon layui-icon-delete"></i></a>';
                            }
                            return html+'</div>';
                    }}
                ]]
            });
            table.on('tool(dataTable)', function(obj){
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'clear') {
                    layer.confirm('确认要清空吗？', function(index) {
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/maintain/database/clear'), { table: data.name }, function (result) {
                            layer.close(index);
                            if (result.code > 0) {
                                return layer.msg(result.message, {icon:2});
                            }
                            dataTable.reload();
                            layer.msg(result.message, {icon:1});
                        });
                    });
                }else if(layEvent === 'delete') {
                    layer.msg("系统不开放删除数据表功能")
                }
            });
            layui.util.event('lay-active', {
                optimize: function(){
                    I.PostDeal(AppGlobalMethods.RouteUrl('admin/maintain/database/optimize'));
                },
                repair: function(){
                    I.PostDeal(AppGlobalMethods.RouteUrl('admin/maintain/database/repair'));
                }
            });
        })
    </script>
@endsection
