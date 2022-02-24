<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin 后台管理系统</title>
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('favicon-32x32.png')}}">
    <link rel="stylesheet" href="{{asset('static/common/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/common/style/login.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/admin/css/loader.css')}}" media="all">
    <style>
        body{ background-image: url("{{asset('static/admin/img/svg/background.svg')}}");background-size: cover; }
        .layadmin-user-login-main{background-color: #fff;-webkit-border-radius: 5px;-moz-border-radius: 5px;border-radius: 5px;}
        .layadmin-user-login {position: absolute;right: 0;margin: 0 auto;}
        .layui-form-item .submit{border-radius: 12px;}
        .layadmin-user-login-main{background-color: rgba(255, 255, 255, .3);box-shadow: 0 0 5px 2px #b3b3b3;border-radius: 12px;}
        .layadmin-user-login-main::before{content:''; position:absolute; top:0; left:0; right:0; bottom:0; filter:blur(10px) contrast(.8); z-index:-1; }
        .slider-item .slider-bg{background-image: linear-gradient(to right,#359FD4,#36B5C8,#25D8AB);}
        .layadmin-user-login-header h2{background-image: linear-gradient(to right,#359FD4,#36B5C8,#25D8AB); -webkit-background-clip: text; color: transparent;}
    </style>
</head>
<body>

<div class="layadmin-user-login layadmin-user-display-show" >
    <div class="layadmin-user-login-main">
        <div class="layadmin-user-login-box layadmin-user-login-header">
            <h2>LaravelAdmin</h2>
            <p>LaravelAdmin 后台管理系统</p>
        </div>
        <div class="layadmin-user-login-box layadmin-user-login-body">
            <div class="layui-form" lay-filter="LoginForm">
                <div class="layui-form-item layui-input-wrap layui-input-wrap-prefix">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-username" for="LAY-user-login-username"></label>
                    <input type="text" name="username" lay-verify="required" lay-vertype="tips" placeholder="账号" class="layui-input" lay-affix="clear">
                </div>
                <div class="layui-form-item layui-input-wrap layui-input-wrap-prefix">
                    <label class="layadmin-user-login-icon layui-icon layui-icon-password" for="LAY-user-login-password"></label>
                    <input type="password" name="password" lay-verify="required" lay-vertype="tips" placeholder="密码" class="layui-input" lay-affix="eye">
                </div>
                <div class="layui-form-item">
                    <div id="slider"></div>
                </div>
                <div class="layui-form-item">
                    <input type="checkbox" name="remember" value="1" lay-skin="primary" title="记住密码" >
                </div>
                <div class="layui-form-item">
                    <button type="button" class="layui-btn layui-btn-fluid submit" lay-active="login">
                        <i class="layui-icon layui-icon-release"></i> 登 录
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="loader-main">
    <div class="loader"></div>
</div>

<script src="{{asset('static/common/layui/layui.js')}}"></script>
<script src="{{asset('static/admin/js/app.js')}}"></script>
<script>
    layui.config({
        base: AppGlobalMethods.BaseUrl()+'/static/common/'
    }).extend({
        index: 'lib/index',
        sliderVerify: 'plugins/sliderVerify',
    }).use(['index', 'sliderVerify'], function () {
        let $ = layui.jquery, layer = layui.layer, form = layui.form, util = layui.util,
            slider = layui.sliderVerify.render({
                elem: '#slider',
                onOk: function() {
                    layer.msg("滑块验证通过");
                }
            }),
            frameElementUrl = self.frameElement ? AppGlobalMethods.RouteUrl($(self.frameElement).attr('src')) : false,
            init = function () {
                $.get(AppGlobalMethods.RouteUrl('admin/check_login'), {}, function (result) {
                    let data = result.data;
                    if (data.isCheck) {
                        frameElementUrl ? location.replace(frameElementUrl) : location.replace(result.data.url);
                    }
                });
            };init();
        util.event('lay-active', {
            login: function(that) {
                if ($(that).attr('condition') != 1) {
                    if(!slider.isOk()) {
                        return layer.msg("请先通过滑块验证");
                    }
                    form.submit('LoginForm', function(data) {
                        let field = data.field;
                        field.username = btoa(field.username);
                        field.password = btoa(field.password);
                        layer.msg('正在处理请求...', { icon: 16, shade: 0.01, time: false });
                        $(that).attr('condition', 1);
                        $.post(location.href, field, function (result) {
                            $(that).attr('condition', 0)
                            if (result.code != 0) {
                                slider.reset();
                                layer.msg('账号密码错误', {icon: 5});return;
                            }
                            layer.msg('登录成功', {icon: 6});
                            frameElementUrl ? location.replace(frameElementUrl) : location.replace(result.data.url);
                        }).error(function (xhr, status, info) {
                            slider.reset();
                            $(".submit").attr('condition', 0);
                            layer.msg("登录异常", {icon: 5});
                        });
                    });
                }
            }
        });
        $.ajaxSetup({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });
        setTimeout(function() {
            $(".loader-main").fadeOut(300);
        }, 1000)
    })
</script>
</body>
</html>
