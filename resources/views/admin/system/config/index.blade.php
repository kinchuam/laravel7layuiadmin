@extends('admin.base')

@section('content')
    <div class="layui-card">

        <div class="layui-card-body">
            <form class="layui-form layui-form-pane" method="post" lay-filter="BaseForm">
                <input type="hidden" name="_method" value="put">
                <div class="layui-form-item">
                    <label class="layui-form-label">名称</label>
                    <div class="layui-input-block">
                        <input type="text" name="webname" placeholder="请输入标题" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">标题</label>
                    <div class="layui-input-block">
                        <input type="text" name="title" placeholder="请输入标题" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">关键词</label>
                    <div class="layui-input-block">
                        <input type="text" name="keywords" placeholder="请输入关键词" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <label class="layui-form-label">描述</label>
                    <div class="layui-input-block">
                        <input type="text" name="description" placeholder="请输入描述" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item layui-form-text">
                    <label class="layui-form-label">版权</label>
                    <div class="layui-input-block">
                        <textarea class="layui-textarea" name="copyright" rows="8"></textarea>
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" lay-filter="BaseFormSubmit"><i class="layui-icon layui-icon-release"></i> 保 存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form, siteKey = 'website';
            $.get(AppGlobalMethods.RouteUrl('admin/config/data'), {siteKey}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl("admin/config/"+siteKey+"/update"));
                form.val('BaseForm', {
                    "webname"       :   data.webname||'',
                    "title"         :   data.title||'',
                    "keywords"      :   data.keywords||'',
                    "description"   :   data.description||'',
                    "copyright"     :   data.copyright||'',
                });
            })
        })
    </script>
@endsection