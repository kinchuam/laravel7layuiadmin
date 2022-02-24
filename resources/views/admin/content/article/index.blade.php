@extends('admin.base')

@section('content')
    <div class="layui-row layui-col-space15" >
        <div class="layui-col-md3">
            <div class="layui-card">
                <div class="layui-card-body" style="padding: 10px;">
                    <fieldset class="layui-elem-field layui-field-title" > <legend>栏目</legend></fieldset>
                    <ul class="layui-menu" id="docDemoMenu"></ul>
<!--                    <div id="organizationTree" ></div>-->
                </div>
            </div>
        </div>
        <div class="layui-col-md9">
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
        </div>
    </div>

@endsection

@section('script')
    <script>
        layui.use(['index','okLayer'],function () {
            let $ = layui.jquery, layer = layui.layer, table = layui.table, okLayer = layui.okLayer, dropdown = layui.dropdown,
                category_id = layui.sessionData('article_cache').category_id||'',
                dataTable = table.render({
                    elem: '#dataTable',
                    url: AppGlobalMethods.RouteUrl('admin/content/articles/data'),
                    page: true,
                    data: [],
                    cols: [[
                        {checkbox: true, fixed: true}
                        ,{field: 'sort', title: '排序', width:90, sort: !0}
                        ,{field: 'thumb', title: '缩略图', event:'preview', unresize: true, width: 90, templet:function (d) {
                            return '<a href="JavaScript:;" title="点击查看"><img src="'+(d.thumb||"")+'" alt="" width="30" height="30" onerror="AppGlobalMethods.imgError(this)"></a>'
                        }}
                        ,{field: 'title', title: '标题'}
                        ,{field: 'status', title: '状态', unresize: true, width:90, align:'center', templet: function (d) {
                            return d.status == 1?'<span class="layui-badge layui-bg-green">显示</span>':'<span class="layui-badge layui-bg-orange">隐藏</span>';
                        }}
                        ,{field: 'created_at', title: '创建时间', width:180}
                        ,{fixed: 'right', width: 160, align:'center', templet: function () {
                            let html = '<div class="layui-btn-group">';
                            if (AppGlobalMethods.RouteUrl('content.articles.edit')) {
                                html += '<a class="layui-btn layui-btn-sm" lay-event="edit" title="编辑"><i class="layui-icon layui-icon-edit"></i></a>';
                            }
                            if (AppGlobalMethods.RouteUrl('content.articles.destroy')) {
                                html += '<a class="layui-btn layui-btn-danger layui-btn-sm" lay-event="del" title="删除"><i class="layui-icon layui-icon-delete"></i></a></a>';
                            }
                            return html+'</div>';
                        }}
                    ]]
                }),
                I = {
                    init() {
                        let html = '';
                        if (AppGlobalMethods.UserPermissions('content.articles.destroy')) {
                            html += '<a class="layui-btn layui-btn-sm layui-btn-danger" lay-filter="destroy" data-url="'+AppGlobalMethods.RouteUrl('admin/content/articles/destroy')+'"><i class="layui-icon layui-icon-delete"></i> 删除</a>';
                        }
                        if (AppGlobalMethods.UserPermissions('content.articles.create')) {
                            html += '<a class="layui-btn layui-btn-sm" lay-filter="create" data-full="true" data-url="'+AppGlobalMethods.RouteUrl('admin/content/articles/create?category_id='+category_id)+'"><i class="layui-icon layui-icon-add-circle"></i> 添加</a>';
                        }
                        $("#ButtonGroup").append(html);
                        this.renderTree();
                    },
                    renderTree() {
                        let that = this;
                        $.get(AppGlobalMethods.RouteUrl('admin/content/articles/category'), {}, function (res) {
                            that.SetData(res.data);
                        })
                    },
                    SetData(data) {
                        let html = '<li class="layui-menu-item-checked"> <div class="layui-menu-body-title"> 全部 </div> </li>';
                        layui.each(data, function (index, item) {
                            html += '<li lay-options="{id: '+item.id+'}">  <div class="layui-menu-body-title"> '+item.title+' </div> </li>';
                        });
                        $("#docDemoMenu").append(html);
                        layui.sessionData('article_cache', {
                            key: 'category_id', value: 0
                        });
                        dropdown.on('click(docDemoMenu)', function(options) {
                            layui.sessionData('article_cache', {
                                key: 'category_id', value: options.id
                            });
                            dataTable.reload({
                                where: {category_id: options.id},
                                page: {curr: 1},
                            });
                        });
                    }
                };I.init();

            table.on('tool(dataTable)', function(obj) {
                let data = obj.data, layEvent = obj.event;
                if(layEvent === 'del') {
                    layer.confirm('确认删除吗？', function(index) {
                        layer.msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                        $.post(AppGlobalMethods.RouteUrl('admin/content/articles/destroy'), {_method:'delete', ids:[data.id]}, function (result) {
                            if (result.code == 0) {
                                obj.del();
                            }
                            layer.close(index);
                            layer.msg(result.message);
                        });
                    });
                } else if(layEvent === 'edit'){
                    okLayer.open('编辑文章【'+data.title+'】', AppGlobalMethods.RouteUrl('admin/content/articles/'+data.id+'/edit'), {full:true});
                } else if(layEvent === 'preview'){
                    if(data.thumb) {
                        layer.photos({
                            photos: {
                                title: "查看",
                                data: [{ src: data.thumb }]
                            },
                            shade: .01,
                            closeBtn: 1,
                            anim: 5
                        });
                    }
                }
            });
        })
    </script>
@endsection

