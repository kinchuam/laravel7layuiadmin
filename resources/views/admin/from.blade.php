<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin - 表单页</title>
    <meta name="keywords" content="LaravelAdmin - 表单页">
    <meta name="description" content="LaravelAdmin - 表单页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="stylesheet" href="{{asset('static/common/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/admin/css/style.css')}}" media="all">
</head>
<body>

<div class="layui-fluid fromPage">
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
        tinymce: 'plugins/tinymce/tinymce',
        xmSelect: 'plugins/xm-select',
        fileLibrary: 'plugins/fileLibrary/fileLibrary'
    }).use(['index'], function () {
        let $ = layui.jquery, layer = layui.layer;
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
