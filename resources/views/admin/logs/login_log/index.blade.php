@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body" lay-filter="SearchForm">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <div id="username"></div>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="ip" placeholder="关键词搜索" class="layui-input" >
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
        layui.use(['index','xmSelect'], function () {
            let $ = layui.jquery;
            layui.table.render({
                elem: '#dataTable'
                ,url: AppGlobalMethods.RouteUrl('admin/logs/login/data')
                ,page: true
                ,cols: [[
                    {field: 'username', title: '管理员', width: 150}
                    ,{field: 'message', title: '描述'}
                    ,{field: 'ip', title: 'IP', width: 138}
                    ,{field: 'ip_address', title: 'IP地址'}
                    ,{field: 'platform_browser', title: '系统信息', width:300, templet:function (d) {
                       return d.platform+' '+(d.platform_version||'')+' '+d.browser+' '+(d.browser_version||'');
                    }}
                    ,{field: 'created_at', title: '创建时间', width:190}
                ]]
            });
            layui.xmSelect.render({
                el: '#username',
                radio: true,
                tips: '请选择管理员',
                name: 'username',
                prop: { value: 'username', name: 'display_name'},
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
        });
    </script>
@endsection
