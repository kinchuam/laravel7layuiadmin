@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15">
        <div class="layui-col-md8">
            <div class="layui-row layui-col-space15">
                <div class="layui-col-md6">
                    <div class="layui-card b2">
                        <div class="layui-card-header">快捷方式</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-shortcut">
                                <div carousel-item id="admin-shortcut"> </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md6">
                    <div class="layui-card">
                        <div class="layui-card-header">统计数</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-backlog" data-autoplay="true" data-interval="5000">
                                <div carousel-item id="data_counts"> </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="layui-col-md12">
                    <div class="layui-card">
                        <div class="layui-card-header">数据概览</div>
                        <div class="layui-card-body">
                            <div class="layui-carousel layadmin-carousel layadmin-dataview" data-anim="fade" lay-filter="LAY-index-dataview" data-height="600px">
                                <div carousel-item id="LAY-index-dataview">
                                        <div><i class="layui-icon layui-icon-loading1 layadmin-loading"></i></div>
                                    <div></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="layui-col-md4">
            <div class="layui-card">
                <div class="layui-card-header">管理员信息</div>
                <div class="layui-card-body">
                    <div class="admin_user">
                        <div class="admin_user_rght">
                            <div class="headimg">
                                <i></i>
                                <a href="JavaScript:;" data-icon="fa-user" data-title="修改信息" class="new_tab"><img src="{{asset('static/admin/img/default_headimg.gif')}}" alt=""></a>
                            </div>
                            <div class="welcome en-font">
                                您好！<span id="welcome-span">-- </span>
                                <a id="user_logout"><i style="color: red;" class="fa fa-sign-out" aria-hidden="true"></i></a>
                            </div>
                        </div>
                        <div class="admin_user_left">
                            <ul class="list">
                                <li>账号：<span class="c">-- </span></li>
                                <li>IP：<span class="c">-- </span></li>
                                <li>地址：<span class="c">-- </span></li>
                                <li>时间：<span class="c">-- </span></li>
                            </ul>
                            <div class="user_link layui-btn-group">
                                <a class="layui-btn layui-btn-primary new_tab basic_index" data-icon="layui-icon-chart-screen">个人信息</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">
                    当前时间
                    <i class="layui-icon layui-icon-tips" lay-tips="时间提示" lay-offset="5"></i>
                </div>
                <div class="layui-card-body layui-text layadmin-text">
                    <blockquote class="layui-elem-quote layui-bg-green">
                        <div id="nowTime"></div>
                    </blockquote>
                </div>
            </div>

            <div class="layui-card">
                <div class="layui-card-header">版本信息</div>
                <div class="layui-card-body" style="min-height: 245px;">
                    <table class="layui-table" style="table-layout: fixed;">
                        <tbody id="widget_config"> </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'echarts'], function () {
            let $ = layui.$, g = layui.carousel, t = layui.admin, s = layui.device(), echarts = layui.echarts,
                r = $("#LAY-index-dataview").children("div"),
                I = {
                    init() {
                        $("#user_logout").attr('href', AppGlobalMethods.RouteUrl('admin/logout'));
                        $(".basic_index").attr('lay-href', AppGlobalMethods.RouteUrl('admin/basic'));
                        if (AppGlobalMethods.UserPermissions('logs.login')) {
                            $(".user_link").append('<a lay-href="'+AppGlobalMethods.RouteUrl('admin/logs/login')+'" class="layui-btn layui-btn-primary new_tab" data-icon="layui-icon-chart-screen">登录日志</a>');
                        }
                        this.GetUser();
                        this.GetData();
                        this.GetLineChart();
                    },
                    GetData() {
                        let that = this;
                        $.get(AppGlobalMethods.RouteUrl('admin/count'), {}, function (result) {
                            that.SetTplData(result.data);
                        });
                    },
                    SetTplData(arr) {
                        let $shortcut = arr.shortcut, $data_counts = arr.data_counts, $widget_config = arr.widget_config;
                        if ($shortcut) {
                            let shortcutHtml = '';
                            for (let i in $shortcut) {
                                shortcutHtml += '<ul class="layui-row layui-col-space10">';
                                for (let e in $shortcut[i]) {
                                    let va = $shortcut[i][e];
                                    shortcutHtml += ' <li class="layui-col-xs3"> <a '+ (va.url?"lay-href="+AppGlobalMethods.RouteUrl(va.url):"" ) +'><i class="layui-icon '+va.icon+'"></i> <cite>'+va.title+'</cite></a> </li>';
                                }
                                shortcutHtml += '</ul>';
                            }
                            $("#admin-shortcut").html(shortcutHtml);
                        }
                        if ($data_counts) {
                            let countHtml = '';
                            for (let i in $data_counts) {
                                countHtml += '<ul class="layui-row layui-col-space10">';
                                for (let e in $data_counts[i]) {
                                    let va = $data_counts[i][e];
                                    countHtml += ' <li class="layui-col-xs6"> <a '+ (va.url?"lay-href="+AppGlobalMethods.RouteUrl(va.url):"" ) +' class="layadmin-backlog-body"> <h3>'+va.title+'</h3> <p><cite>'+(va.count||0)+'</cite></p> </a> </li>';
                                }
                                countHtml += '</ul>';
                            }
                            $("#data_counts").html(countHtml);
                        }
                        if ($widget_config) {
                            let widgetHtml = '';
                            for (let i in $widget_config) {
                                let color = '';
                                if (i%2 === 0) {
                                    color = 'style="background-color: #f2f2f2;"';
                                }
                                widgetHtml += '<tr '+color+'> <th>'+$widget_config[i][0]+'</th> <th class="layui-elip">'+$widget_config[i][1]+'</th> <th>'+$widget_config[i][2]+'</th> </tr>'
                            }
                            $("#widget_config").html(widgetHtml);
                        }
                        this.UpdateCarousel();
                    },
                    GetLineChart() {
                        let that = this;
                        $.get(AppGlobalMethods.RouteUrl('admin/line_chart'), {}, function (result) {
                            let data = result.data;
                            let l = [],
                                n = [{title:{text:data.platform.title},tooltip:{trigger:'axis',axisPointer:{type:'cross',label:{backgroundColor:'#6a7985'}}},legend:{data:['PV','UV']},toolbox:{feature:{saveAsImage:{}}},grid:{left:'3%',right:'4%',bottom:'3%',containLabel:true},xAxis:[{type:'category',boundaryGap:false,data:data.platform.keys}],yAxis:[{type:'value'}],series:[{name:'PV',type:'line',stack:'总量',areaStyle:{},data:data.platform.pv},{name:'UV',type:'line',stack:'总量',areaStyle:{},data:data.platform.uv}]},{title:{text:data.browser.title,x:"center",textStyle:{fontSize:14}},tooltip:{trigger:"item",formatter:"{a} <br/>{b} : {c} ({d}%)"},legend:{orient:"vertical",x:"left",data:data.browser.keys},series:[{name:"访问来源",type:"pie",radius:"55%",center:["50%","50%"],data:data.browser.data}]}],
                                o = function (e) {
                                    l[e] = echarts.init(r[e], layui.echartsTheme);
                                    l[e].setOption(n[e]);
                                    t.resize(function () { l[e].resize(); })
                                };
                            if (r[0]) {
                                let d = 0;o(0);
                                g.on("change(LAY-index-dataview)", function(e){ o(d = e.index) });
                                t.on("side", function(){ setTimeout(function() { o(d) }, 300) });
                                t.on("hash(tab)", function(){ layui.router().path.join("")||o(d) });
                            }
                            that.UpdateCarousel();
                        });
                    },
                    UpdateCarousel() {
                        $(".layadmin-carousel").each(function () {
                            let a = $(this);
                            g.render({
                                elem: this,
                                width: "100%",
                                arrow: "none",
                                interval: a.data("interval"),
                                autoplay: a.data("autoplay") === !0,
                                trigger: s.ios || s.android ? "click" : "hover",
                                anim: a.data("anim")
                            })
                        });
                    },
                    GetUser() {
                        let localData = layui.sessionData('AdminSystem'), user = localData.SystemUser;
                        if (user) {
                            let $t = $('.admin_user_left ul li');
                            $('#welcome-span').text(user.display_name||'--');
                            $t.eq(0).find('.c').text(user.username||'--');
                            $t.eq(1).find('.c').text(user.ip||'--');
                            $t.eq(2).find('.c').text(user.ip_data||'--');
                            $t.eq(3).find('.c').text(user.created_at||'--');
                            this.SetLangDate();
                        }
                    },
                    SetLangDate() {
                        let that = this
                            , dateObj = new Date()
                            , dateFilter = function (date) {return (date < 10) ? "0" + date : date;}
                            , year = dateObj.getFullYear()
                            , month = dateObj.getMonth() + 1
                            , date = dateObj.getDate()
                            , day = dateObj.getDay()
                            , hour = dateObj.getHours()
                            , minute = dateObj.getMinutes()
                            , second = dateObj.getSeconds()
                            , weeks = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"]
                            , newDate = dateFilter(year) + "年" + dateFilter(month) + "月" + dateFilter(date) + "日 " + dateFilter(hour) + ":" + dateFilter(minute) + ":" + dateFilter(second)
                            , html = "亲爱的"+ $("#welcome-span").text() +"， " + ((hour >= 12) ? (hour >= 18) ? "晚上" : "下午" : "上午") + "好！<br/> " + newDate + "　" + weeks[day];
                        $("#nowTime").html(html);
                        setTimeout(function () {
                            that.SetLangDate();
                        }, 1000);
                    }
                };I.init();
        });
    </script>
@endsection
