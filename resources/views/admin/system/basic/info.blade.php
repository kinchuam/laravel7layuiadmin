@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">信息</div>
        <div class="layui-card-body" pad15>
            <form class="layui-form layui-form-pane" method="post" lay-filter="BaseForm">
                <div class="layui-form-item">
                    <label for="" class="layui-form-label">昵称</label>
                    <div class="layui-input-block">
                        <input type="text" name="display_name" lay-verify="required" lay-vertype="tips" placeholder="请输入昵称" class="layui-input">
                    </div>
                </div>
                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" lay-filter="BaseFormSubmit"><i class="layui-icon layui-icon-release"></i> 保存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            $.get(AppGlobalMethods.RouteUrl('admin/userInfo'), {}, function (res) {
                let data = res.data;
                form.val('BaseForm', {
                    "display_name": data.display_name||'',
                });
            })
        })
    </script>
@endsection
