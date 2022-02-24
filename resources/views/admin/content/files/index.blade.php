@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-form layui-card-body" lay-filter="SearchForm">
            <div class="layui-form-item">
                <div class="layui-input-inline">
                    <input type="text" name="keywords" id="keywords" placeholder="文件名" class="layui-input">
                </div>
                <div class="layui-input-inline">
                    <button type="button" class="layui-btn" lay-filter="search">
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
    <script src="{{asset('static/admin/js/clipboard.min.js')}}"></script>
    <script>
        layui.use(['flow','table'], function () {
            let $ = layui.jquery, flow = layui.flow, table = layui.table,
                I = {
                    init() {
                        let html = '';
                        if (AppGlobalMethods.UserPermissions('content.files.destroy')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-filter="destroy" data-url="'+AppGlobalMethods.RouteUrl('admin/content/files/destroy')+'"><i class="layui-icon layui-icon-delete"></i> 删除</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('content.files.create')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-title="上传文件" data-btn="false" data-width="80%" data-url="'+AppGlobalMethods.RouteUrl('admin/content/files/create')+'"><i class="layui-icon layui-icon-add-circle"></i> 上传</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('content.files.recycle')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-normal" lay-filter="recycle" data-title="回收站" data-btn="false" data-full="true" data-url="'+AppGlobalMethods.RouteUrl('admin/content/files/recycle')+'"><i class="fa fa-recycle"></i> 回收站</a>';
                        }
                        $("#ButtonGroup").append(html);
                    }
                };I.init();
            table.render({
                elem: '#dataTable'
                ,url: AppGlobalMethods.RouteUrl('admin/content/files/data')
                ,page: true
                ,cols: [[
                    {checkbox: true, fixed: true}
                    ,{field: 'filename', title: '文件名'}
                    ,{field: 'type', title: '类型', width: 100, event:'preview', templet: function (d) {
                        let str = d.file_type, html = '<a href="JavaScript:;" title="点击查看"> \n';
                        if (str.indexOf("image") !== -1 ) {
                            html += '<img lay-src="'+ d.file_url +'" style="vertical-align:middle;" alt="'+d.filename+'" width="28" height="28" onerror="AppGlobalMethods.imgError(this)">\n';
                        }else{
                            html += '<img src="'+AppGlobalMethods.RouteUrl('static/admin/img/ico/')+d.suffix+'.png" style="vertical-align:middle;" alt="'+d.filename+'" width="28" height="28" onerror="AppGlobalMethods.imgError(this)">\n';
                        }
                        html += '</a>';
                        return html;
                    }}
                    ,{field: 'size', title: '文件大小', width: 120, templet:function (d) { return AppGlobalMethods.GetFileSize(d.size); }}
                    ,{field: 'storage', title: '储存方式', width: 100}
                    ,{field: 'created_at', title: '创建时间', width: 180}
                    ,{fixed: 'right', width: 180, align:'center', templet: function (d) {
                        let html = '<div class="layui-btn-group">';
                        html += '<a class="layui-btn layui-btn-sm layui-btn-normal copy" lay-event="copy" data-clipboard-text="'+d.file_url+'" >复制</a>';
                        html += '<a class="layui-btn layui-btn-sm" lay-event="download" >下载</a>';
                        if (AppGlobalMethods.UserPermissions('content.files.destroy')) {
                            html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del"><i class="layui-icon layui-icon-delete"></i></a>';
                        }
                        return html+'</div>';
                    }}
                ]]
                ,done: function () {
                    flow.lazyimg();
                }
            });
            table.on('tool(dataTable)', function(obj){
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del') {
                    layer.confirm('确认删除吗？', function(index) {
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/content/files/destroy'), {_method:'delete', ids:[data.id]}, function (result) {
                            if (result.code == 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                }else if(layEvent === 'preview') {
                    if(data.path){
                        if(data.file_type.indexOf("video") !== -1) {
                            layer.open({
                                type: 2,
                                title: false,
                                area: ['530px', '360px'],
                                shade: 0.8,
                                closeBtn: 0,
                                shadeClose: true,
                                content: data.file_url
                            });
                        }else if (data.file_type.indexOf("image") !== -1) {
                            layer.photos({
                                photos: {
                                    title: "查看",
                                    data: [{ src: data.file_url }]
                                },
                                shade: .01,
                                closeBtn: 1,
                                anim: 5
                            });
                        }
                    }
                } else if (layEvent === 'copy') {
                    let clipboard = new ClipboardJS('.copy');
                    clipboard.on('success', function(e) {
                        layer.msg('复制成功');
                        clipboard.destroy();
                        e.clearSelection();
                    });
                } else if (layEvent === 'download') {
                    let index = layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                    if (AppGlobalMethods.download(data.file_url, data.filename)) {
                        layer.close(index);
                    }
                }
            });
        })
    </script>
@endsection
