<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin - 内页</title>
    <meta name="keywords" content="LaravelAdmin - 内页">
    <meta name="description" content="LaravelAdmin - 内页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="{{asset('static/common/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/common/style/admin.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/admin/css/style.css')}}" media="all">
</head>
<body>

<div class="layui-fluid">
    @yield('content')
</div>

<div class='ok-loading'>
    <div class='ball-loader'>
        <span></span>
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<script src="{{asset('static/common/layui/layui.js')}}"></script>
<script src="{{asset('static/admin/js/app.js')}}"></script>
<script>
    layui.config({
        base: AppGlobalMethods.BaseUrl()+'/static/common/'
    }).extend({
        index: 'lib/index',
        inputTag: 'plugins/inputTag/inputTag',
        opTable: 'plugins/opTable/opTable.min',
        treeTable: 'plugins/treeTable',
        xmSelect: 'plugins/xm-select',
        okLayer: 'plugins/okLayer',
    }).use(['index', 'okLayer'],function () {
        let $ = layui.jquery, form = layui.form, layer = layui.layer, table = layui.table,
            B = {
                OpenLayer(othis) {
                    let that = $(othis[0]),
                        title = that.data("title"),
                        btn = that.data("btn"),
                        full = that.data("full"),
                        width = that.data("width"),
                        height = that.data("height"),
                        url = $(othis[0]).data("url"),
                        eml = {full};
                    if (btn !== undefined) { eml.btn = btn; }
                    if (width) { eml.width = width; }
                    if (height) { eml.height = height; }
                    layui.okLayer.open(title||'操作', url, eml);
                },
                BatchOpera(othis, text, data = {}) {
                    let ids = [],hasCheck = table.checkStatus('dataTable'), hasCheckData = hasCheck.data, url = $(othis[0]).data("url");
                    if (hasCheckData.length > 0) {
                        $.each(hasCheckData, function (index, element) {
                            ids.push(element.id);
                        });
                    }
                    if (ids.length > 0) {
                        layer.confirm(text, function(index) {
                            layer.msg('正在请求...', { icon: 16, shade: 0.01, time: false });
                            Object.assign(data, {ids});
                            $.post(url, data, function (result) {
                                if (result.code === 0) {
                                    table.reload('dataTable')
                                }
                                layer.close(index);
                                layer.msg(result.message||"系统错误");
                            });
                        });
                        return;
                    }
                    layer.msg('请选择操作项', {icon:5});
                }
            };
        layui.util.event('lay-filter', {
            create(othis) {
                B.OpenLayer(othis);
            },
            destroy(othis) {
                B.BatchOpera(othis, '确认删除吗？', {_method:'delete'});
            },
            recycle(othis) {
                B.OpenLayer(othis);
            },
            recover(othis) {
                B.BatchOpera(othis, '确认恢复吗？');
            },
            search() {
                form.submit('SearchForm', function(data) {
                    table.reload('dataTable', {
                        where: data.field,
                        page: {curr:1}
                    })
                });
            },
            updateCache(othis) {
                let url = $(othis[0]).data("url");
                layer.confirm('确定更新缓存么？', function() {
                    layer.msg('正在请求...', { icon: 16, shade: 0.01, time: false });
                    $.post(url, {}, function (result) {
                        result.code === 0 ? layer.msg(result.message, {time: 2000, icon: 6}) : layer.msg(result.message||"系统错误", {time: 3000, icon: 5});
                    });
                });
            },
            BaseFormSubmit() {
                layer.confirm('确定提交么？', function() {
                    form.submit('BaseForm', function(data) {
                        layer.msg('正在请求...', { icon: 16, shade: 0.01, time: false });
                        $.post(data.form.action, data.field, function (result) {
                            result.code === 0 ? layer.msg(result.message, {time: 2000, icon: 6}) : layer.msg(result.message||"系统错误", {time: 3000, icon: 5});
                        });
                    });
                });
            }
        });
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            error(xhr) {
                layer.msg(AppGlobalMethods.responseText(xhr), {time: 3000, icon: 5});
            }
        });
        setTimeout(function() {
            $(".ok-loading").fadeOut(300);
        }, 1000);
    });
</script>
@yield('script')
</body>
</html>