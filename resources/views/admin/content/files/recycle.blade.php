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
        layui.use(['index',], function () {
            let $ = layui.jquery, table = layui.table,
                I = {
                    init() {
                        let html = '';
                        if (AppGlobalMethods.UserPermissions('content.files.expurgate')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-filter="destroy" data-url="'+AppGlobalMethods.RouteUrl('admin/content/files/expurgate')+'"><i class="layui-icon layui-icon-delete"></i> 删除</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('content.files.recover')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-filter="recover" data-url="'+AppGlobalMethods.RouteUrl('admin/content/files/recover')+'">恢复</a>';
                        }
                        $("#ButtonGroup").append(html);
                    }
                };I.init();
            table.render({
                elem: '#dataTable'
                ,url: AppGlobalMethods.RouteUrl('admin/content/files/data')
                ,where:{recycle:1}
                ,page: true
                ,cols: [[
                    {checkbox: true, fixed: true}
                    ,{field: 'filename', title: '文件名'}
                    ,{field: 'size', title: '文件大小', width: 100, templet:function (d) { return AppGlobalMethods.GetFileSize(d.size); }}
                    ,{field: 'storage', title: '储存方式', width: 90}
                    ,{field: 'type', title: '文件类型', width: 110}
                    ,{field: 'deleted_at', title: '删除时间', width: 200}
                    ,{fixed: 'right', width: 150, align:'center', templet: function () {
                        let html = '<div class="layui-btn-group">';
                        if (AppGlobalMethods.UserPermissions('content.files.recover')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-event="recover">恢复</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('content.files.expurgate')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i> </a>';
                        }
                        return html+'</div>';
                    }}
                ]]
            });
            table.on('tool(dataTable)', function(obj) {
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del') {
                    layer.confirm('确认删除吗？', function(index) {
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/content/files/expurgate'), {_method:'delete', ids:[data.id]}, function (result) {
                            if (result.code == 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                }else if(layEvent === 'recover') {
                    layer.confirm('确认恢复吗？', function(index) {
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/content/files/recover'), {ids:[data.id]}, function (result) {
                            if (result.code == 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                }
            });
        })
    </script>
@endsection
