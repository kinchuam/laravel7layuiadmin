@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body" lay-filter="SearchForm">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <div id="causer_id"></div>
                    </div>
                </div>
                <div class="layui-inline">
                    <button type="button" class="layui-btn" lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','okLayer','xmSelect'],function () {
            let $ = layui.jquery, okLayer = layui.okLayer, table = layui.table;
            table.render({
                elem: '#dataTable'
                ,url: AppGlobalMethods.RouteUrl('admin/logs/operation/data')
                ,page: true
                ,cols: [[
                    {field: 'log_name', title: '模块', width: 120}
                    ,{field: 'username', title: '管理员', width: 180, templet: function (d) {
                        return d.user?d.user.display_name:'--';
                    }}
                    ,{field: 'description', title: '描述'}
                    ,{field: 'subject_type', title: '模型', width: 300}
                    ,{field: 'created_at', title: '创建时间', width: 180}
                    ,{fixed: 'right', width: 100, align:'center', templet: function () {
                        let html = '<div class="layui-btn-group">';
                        if (AppGlobalMethods.UserPermissions('logs.operation.show')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-event="show" title="查看"><i class="layui-icon layui-icon-survey"></i></a>';
                        }
                        return html+'</div>';
                    }}
                ]]
            });
            layui.xmSelect.render({
                el: '#causer_id',
                radio: true,
                tips: '请选择管理员',
                name: 'causer_id',
                prop: { value: 'id', name: 'display_name' },
                model: { icon: 'hidden', label: { type: 'text', } },
                clickClose: true,
                paging: true,
                pageRemote: true,
                pageEmptyShow: false,
                remoteMethod: function(val, cb, show, pageIndex){
                    $.get(AppGlobalMethods.RouteUrl('admin/system/user/data'), {page: pageIndex}, function(res) {
                        cb(res.data, res.count)
                    })
                }
            })
            table.on('tool(dataTable)', function(obj) {
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'show') {
                    okLayer.open('详情', AppGlobalMethods.RouteUrl('admin/logs/operation/'+data.id+'/show'), {btn: false, width: '70%', height: '60%'});
                }
            });
        });
    </script>
@endsection
