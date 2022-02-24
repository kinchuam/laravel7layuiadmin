<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="utf-8">
    <title>LaravelAdmin - 主页</title>
    <meta name="keywords" content="LaravelAdmin - 主页">
    <meta name="description" content="LaravelAdmin - 主页">
    <meta name="renderer" content="webkit">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('favicon-32x32.png')}}">
    <link rel="stylesheet" href="{{asset('static/common/layui/css/layui.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/common/style/admin.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/admin/css/style.css')}}" media="all">
    <link rel="stylesheet" href="{{asset('static/admin/css/loader.css')}}" media="all">
</head>
<body class="layui-layout-body">

<div id="LAY_app">
    <div class="layui-layout layui-layout-admin">
        <div class="layui-header">
            <!-- 头部区域 -->
            <ul class="layui-nav layui-layout-left">
                <li class="layui-nav-item layadmin-flexible" lay-unselect>
                    <a layadmin-event="flexible" title="侧边伸缩">
                        <i class="layui-icon layui-icon-shrink-right" id="LAY_app_flexible"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a id="website" target="_blank" title="前台">
                        <i class="layui-icon layui-icon-website"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a layadmin-event="refresh" title="刷新">
                        <i class="layui-icon layui-icon-refresh-3"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <input type="text" placeholder="搜索..." class="layui-input layui-input-search" layadmin-event="serach" lay-action="https://cn.bing.com/search?q=">
                </li>
            </ul>
            <ul class="layui-nav layui-layout-right" lay-filter="layadmin-layout-right">
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a layadmin-event="note">
                        <i class="layui-icon layui-icon-note"></i>
                    </a>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a layadmin-event="fullscreen">
                        <i class="layui-icon layui-icon-screen-full"></i>
                    </a>
                </li>
                <li class="layui-nav-item" lay-unselect>
                    <a id="layui-admin-username"><cite></cite></a>
                    <dl class="layui-nav-child admin-user-menus">
                        <dd><a>基本资料</a></dd>
                        <dd><a>修改密码</a></dd>
                        <hr/>
                        <dd lay-active="logout"><a>退出</a></dd>
                    </dl>
                </li>
                <li class="layui-nav-item layui-hide-xs" lay-unselect>
                    <a layadmin-event="theme"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
                <li class="layui-nav-item layui-show-xs-inline-block layui-hide-sm" lay-unselect>
                    <a layadmin-event="more"><i class="layui-icon layui-icon-more-vertical"></i></a>
                </li>
            </ul>
        </div>

        <!-- 侧边菜单 -->
        <div class="layui-side layui-side-menu">
            <div class="layui-side-scroll">
                <div class="layui-logo"> <span>LaravelAdmin</span> </div>
                <ul lay-shrink="all" id="LAY-system-side-menu" lay-filter="layadmin-system-side-menu">
                    <li data-name="home" class="layui-nav-item layui-nav-itemed">
                        <a lay-tips="主页" lay-direction="2">
                            <i class="layui-icon layui-icon-home"></i>
                            <cite>主页</cite>
                        </a>
                        <dl class="layui-nav-child">
                            <dd data-name="console" class="layui-this">
                                <a lay-href="admin/index">控制台</a>
                            </dd>
                        </dl>
                    </li>
                </ul>
            </div>
        </div>

        <!-- 页面标签 -->
        <div class="layadmin-pagetabs" id="LAY_app_tabs">
            <div class="layui-icon layadmin-tabs-control layui-icon-prev" layadmin-event="leftPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-next" layadmin-event="rightPage"></div>
            <div class="layui-icon layadmin-tabs-control layui-icon-down">
                <ul class="layui-nav layadmin-tabs-select" lay-filter="layadmin-pagetabs-nav">
                    <li class="layui-nav-item" lay-unselect>
                        <a></a>
                        <dl class="layui-nav-child layui-anim-fadein">
                            <dd layadmin-event="closeThisTabs"><a>关闭当前标签页</a></dd>
                            <dd layadmin-event="closeOtherTabs"><a>关闭其它标签页</a></dd>
                            <dd layadmin-event="closeAllTabs"><a>关闭全部标签页</a></dd>
                        </dl>
                    </li>
                </ul>
            </div>
            <div class="layui-tab" lay-unauto lay-allowClose="true" lay-filter="layadmin-layout-tabs">
                <ul class="layui-tab-title" id="LAY_app_tabsheader">
                    <li lay-id="admin/index" lay-attr="admin/index" class="layui-this"><i class="layui-icon layui-icon-home"></i></li>
                </ul>
            </div>
        </div>
        <!-- 主体内容 -->
        <div class="layui-body" id="LAY_app_body">
            <div class="layadmin-tabsbody-item layui-show">
                <iframe src="admin/index"  frameborder="0" class="layadmin-iframe"></iframe>
            </div>
        </div>
        <!-- 辅助元素，一般用于移动设备下遮罩 -->
        <div class="layadmin-body-shade" layadmin-event="shade"></div>
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
        index: 'lib/index'
    }).use(['index'], function () {
        let $ = layui.jquery, dropdown = layui.dropdown, admin = layui.admin,
            Y = {
                SetPageUrl() {
                    let admin_user_menu = $(".admin-user-menus dd");
                    admin_user_menu.eq(0).find("a").attr('lay-href', AppGlobalMethods.RouteUrl('admin/basic'));
                    admin_user_menu.eq(1).find("a").attr('lay-href', AppGlobalMethods.RouteUrl('admin/basicPassword'));
                    $("#website").attr('href', AppGlobalMethods.RouteUrl('/'));
                },
                init: function() {
                    this.SetPageUrl();
                    this.GetUser();
                    this.GetPermission();
                    layui.sessionData('AdminSystem', {
                        key: 'fileLibrary', value: {
                            FileUpload: AppGlobalMethods.RouteUrl('admin/fileUpload'),
                            FileList: AppGlobalMethods.RouteUrl('admin/content/files/data'),
                            DeleteFiles: AppGlobalMethods.RouteUrl('admin/content/files/destroy'),
                            MoveFiles: AppGlobalMethods.RouteUrl('admin/content/files_group/moveFiles'),
                            GetGroup: AppGlobalMethods.RouteUrl('admin/content/files_group/data'),
                            AddGroup: AppGlobalMethods.RouteUrl('admin/content/files_group/store'),
                            EditGroup: AppGlobalMethods.RouteUrl('admin/content/files_group/update'),
                            DeleteGroup: AppGlobalMethods.RouteUrl('admin/content/files_group/destroy'),
                        }
                    });
                },
                GetMenus() {
                    let that = this;
                    $.get(AppGlobalMethods.RouteUrl('admin/navigation'), {}, function (res) {
                        that.SetMenus(res.data);
                    })
                },
                SetMenus(data) {
                    let html = '';
                    layui.each(data, function (index, $menu) {
                        html += '<li data-name="'+$menu.name+'" class="layui-nav-item">'
                             + '<a '+($menu.url ? "lay-href="+$menu.url : "")+' lay-tips="'+$menu.display_name+'" lay-direction="2">'
                             + '<i class="layui-icon '+$menu.icon+'"></i> <cite>'+$menu.display_name+'</cite>'
                             + '</a>';
                        if($menu.child) {
                            html += '<dl class="layui-nav-child">';
                            layui.each($menu.child, function (index1, $subMenu) {
                                html += '<dd data-name="'+$subMenu.name+'" >'
                                     + '<a lay-href="'+$subMenu.url+'">'+$subMenu.display_name+'</a>';
                                    if($subMenu.child) {
                                        html += '<dl class="layui-nav-child">';
                                        layui.each($menu.child, function (index2, $threeMenu) {
                                            html += '<dd data-name="'+$threeMenu.name+'" > <a lay-href="'+$threeMenu.url+'">'+$threeMenu.display_name+'</a> </dd>';
                                        })
                                        html += '</dl>';
                                    }
                                html += '</dd>';
                            })
                            html += '</dl>';
                        }
                        html += '</li>';
                    })
                    $("#LAY-system-side-menu").addClass('layui-nav').addClass('layui-nav-tree').append(html);
                    layui.element.render('nav', 'layadmin-system-side-menu');
                    this.SetDropdown();
                },
                GetUser() {
                    $.get(AppGlobalMethods.RouteUrl('admin/userInfo'), {}, function (res) {
                        let data = res.data;
                        layui.sessionData('AdminSystem', {
                            key: 'SystemUser', value: data
                        });
                        $("#layui-admin-username cite").text(data.username||'--');
                    })
                },
                GetPermission() {
                    $.get(AppGlobalMethods.RouteUrl('admin/userPermissions'), {}, function (res) {
                        layui.sessionData('AdminSystem', {
                            key: 'UserPermissions', value: res.data
                        });
                    });
                    this.GetMenus();
                },
                SetDropdown() {
                    dropdown.render({
                        elem: '#LAY_app_tabsheader li:gt(0)'
                        , trigger: 'contextmenu'
                        , isAllowSpread: false
                        , id: 'LAY_app_dropdown'
                        , data: [
                            {title: '关闭当前标签页', event: 'closeThisTabs'},
                            {title: '关闭其它标签页', event: 'closeOtherTabs'},
                            {title: '关闭全部标签页', event: 'closeAllTabs'}
                        ]
                        , click(obj) {
                            let p = "#LAY_app_body", x = "layadmin-tabsbody-item", T = '#LAY_app_tabsheader>li',
                                A = {
                                    tabsBody: function(e) {
                                        return $(p).find("." + x).eq(e || 0)
                                    },
                                    closeOtherTabs: function(e) {
                                        if (e === "all") {
                                            $(T + ":gt(0)").remove();
                                            $(p).find("." + x + ":gt(0)").remove();
                                            $(T).eq(0).trigger("click");
                                            return;
                                        }
                                        let t = "LAY-system-pagetabs-remove";
                                        $(T).each(function(k, i) {
                                            if (k > 0 && k !== e) {
                                                $(i).addClass(t);
                                                A.tabsBody(k).addClass(t)
                                            }
                                        })
                                        $(T).eq(e).trigger("click");
                                        $("." + t).remove();
                                    },
                                };
                            if (obj.event === 'closeThisTabs') {
                                admin.closeThisTabs();
                            }else if (obj.event === 'closeOtherTabs') {
                                A.closeOtherTabs($(T).index(this.elem));
                            }else if (obj.event === 'closeAllTabs') {
                                A.closeOtherTabs('all');
                            }
                        }
                    });
                    $('#LAY_app_tabsheader').on('DOMNodeInserted', function() {
                        dropdown.reload('LAY_app_dropdown', {
                            elem: '#LAY_app_tabsheader li:gt(0)'
                        });
                    });
                },
            };Y.init();
        layui.util.event('lay-active', {
            logout() {
                layer.confirm('确认退出登录吗？', function(index) {
                    layer.msg('正在处理请求...', { icon: 16, shade: 0.01, time: false });
                    $.get(AppGlobalMethods.RouteUrl('admin/logout'), {}, function () {
                        layer.close(index);location.reload();
                    })
                })
            }
        });
        setTimeout(function() {
            $(".loader-main").fadeOut(300);
        }, 1000);
    });
</script>
</body>
</html>
