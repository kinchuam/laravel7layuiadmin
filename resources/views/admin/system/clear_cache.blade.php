@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body" pad15="">
            <blockquote class="layui-elem-quote layui-quote-nm">
                <p><i class="layui-icon layui-icon-about"></i> 数据缓存: Remove the configuration cache file</p>
                <p><i class="layui-icon layui-icon-about"></i> 视图缓存: Clear all compiled view files</p>
                <p><i class="layui-icon layui-icon-about"></i> 路由缓存: Remove the route cache file</p>
                <p><i class="layui-icon layui-icon-about"></i> 配置缓存: Remove the configuration cache file</p>
            </blockquote>
            <form class="layui-form layui-form-pane" method="post" lay-filter="BaseForm">
                <input type="hidden" name="_method" value="put">
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">选择</label>
                    <div class="layui-input-block">
                        <input type="checkbox" name="cache" title="数据缓存">
                        <input type="checkbox" name="picture" title="图片缓存">
                        <input type="checkbox" name="view" title="视图缓存">
                        <input type="checkbox" name="route" title="路由缓存">
                        <input type="checkbox" name="config" title="配置缓存">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" lay-active="BaseFormSubmit"><i class="layui-icon layui-icon-release"></i> 提 交</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, layer = layui.layer;
            layui.util.event('lay-active', {
                BaseFormSubmit() {
                    if (!$("input[type='checkbox']").is(':checked')) {
                        return layer.msg("请选择选清除项");
                    }
                    layer.confirm('确定提交么？', function() {
                        layui.form.submit('BaseForm', function(data) {
                            layer.msg('正在请求...', { icon: 16, shade: 0.01, time: false });
                            $.post(AppGlobalMethods.RouteUrl("admin/system/cache/clear"), data.field, function (result) {
                                result.code === 0 ? layer.msg(result.message, {time: 2000, icon: 6}) : layer.msg(result.message||"系统错误", {time: 3000, icon: 5});
                            });
                        });
                    });
                }
            })
        })
    </script>
@endsection