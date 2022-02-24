@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body" lay-filter="SearchForm">
            <div class="layui-form-item">
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <select name="method">
                            <option value="">Method</option>
                            <option value="GET">GET</option>
                            <option value="POST">POST</option>
                            <option value="PUT">PUT</option>
                            <option value="DELETE">DELETE</option>
                            <option value="OPTIONS">OPTIONS</option>
                            <option value="PATCH">PATCH</option>
                            <option value="LINK">LINK</option>
                            <option value="UNLINK">UNLINK</option>
                            <option value="COPY">COPY</option>
                            <option value="HEAD">HEAD</option>
                            <option value="PURGE">PURGE</option>
                        </select>
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="path" placeholder="Path" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <div class="layui-input-inline">
                        <input type="text" name="ip" placeholder="IP" class="layui-input">
                    </div>
                </div>
                <div class="layui-inline">
                    <button type="button" class="layui-btn" lay-filter="search">
                        <i class="layui-icon layui-icon-search layuiadmin-button-btn"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="layui-card-body">
            <table id="dataTable" lay-filter="dataTable"></table>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','okLayer'], function () {
            let table = layui.table, okLayer = layui.okLayer;
            table.render({
                elem: '#dataTable'
                , url: AppGlobalMethods.RouteUrl('admin/logs/access/data')
                , page: true
                , cols: [[
                    {field: 'method', title: '请求方式', width:90, templet: function (it) {
                        return '<span class="layui-badge" style="background-color: '+it.method_color+';">'+it.method+'</span>'
                    }}
                    ,{field: 'path', title: '请求地址'}
                    ,{field: 'ip', title: 'IP', width: 138}
                    ,{field: 'ip_address', title: 'IP地址'}
                    ,{field: 'platform_browser', title: '系统信息', width:300, templet:function (d) {
                        return d.platform+' '+(d.platform_version||'')+' '+d.browser+' '+(d.browser_version||'');
                    }}
                    ,{field: 'created_at', title: '创建时间', width: 180}
                    ,{fixed: 'right', width: 100, align:'center', templet: function () {
                        let html = '<div class="layui-btn-group">';
                        if (AppGlobalMethods.UserPermissions('logs.access.show')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-event="show" title="查看"><i class="layui-icon layui-icon-survey"></i></a>';
                        }
                        return html+'</div>';
                    }}
                ]]
            });
            table.on('tool(dataTable)', function(obj) {
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'show') {
                    okLayer.open('详情', AppGlobalMethods.RouteUrl('admin/logs/access/'+data.id+'/show'), {btn: false, width: '80%', height: '70%'});
                }
            });
        });
    </script>
@endsection
