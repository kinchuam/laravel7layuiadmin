@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body" lay-filter="SearchForm">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input type="text" name="keywords"  placeholder="请输入关键词" class="layui-input">
                </div>
                <div class="layui-input-inline" style="margin-top: -4px;">
                    <button class="layui-btn" lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>
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
        layui.use(['index','okLayer'],function () {
            let $ = layui.jquery, table = layui.table, okLayer = layui.okLayer,
                I = {
                init () {
                    let html = '';
                    if (AppGlobalMethods.UserPermissions('content.article_category.destroy')) {
                        html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-filter="destroy" data-url="'+AppGlobalMethods.RouteUrl('admin/content/article_category/destroy')+'"><i class="layui-icon layui-icon-delete"></i> 删除</a>';
                    }
                    if (AppGlobalMethods.UserPermissions('content.article_category.create')) {
                        html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-url="'+AppGlobalMethods.RouteUrl('admin/content/article_category/create')+'"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>';
                    }
                    $("#ButtonGroup").append(html);
                },
            };I.init();
            table.render({
                elem: '#dataTable'
                ,url: AppGlobalMethods.RouteUrl('admin/content/article_category/data')
                ,page: true
                ,cols: [[
                    {checkbox: true,fixed: true}
                    ,{field: 'sort', title: '排序', sort: !0, width: 110}
                    ,{field: 'name', title: '名称'}
                    ,{field: 'status', title: '状态', width: 120, templet: function (d) {
                        return d.status == 1?'<span class="layui-badge layui-bg-green">显示</span>':'<span class="layui-badge layui-bg-orange">隐藏</span>';
                    }}
                    ,{field: 'created_at', title: '添加时间', width: 200}
                    ,{fixed: 'right', width: 180, align:'center', templet: function () {
                        let html = '<div class="layui-btn-group">';
                        if (AppGlobalMethods.RouteUrl('content.article_category.edit')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-event="edit"><i class="layui-icon layui-icon-edit"></i></a>';
                        }
                        if (AppGlobalMethods.RouteUrl('content.article_category.destroy')) {
                            html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del"><i class="layui-icon layui-icon-delete"></i></a>';
                        }
                        return html+'</div>';
                    }}
                ]]
            });
            table.on('tool(dataTable)', function(obj){
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del') {
                    layer.confirm('确认删除吗？', function (index) {
                        layer.msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/content/article_category/destroy'), {_method: 'delete', ids: [data.id]}, function (result) {
                            if (result.code == 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                } else if(layEvent === 'edit') {
                    okLayer.open('编辑', AppGlobalMethods.RouteUrl('admin/content/article_category/'+data.id+'/edit'));
                }
            });
        })
    </script>
@endsection
