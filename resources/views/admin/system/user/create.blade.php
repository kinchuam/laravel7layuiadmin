@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.system.user._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            $(".layui-form").attr('action', AppGlobalMethods.RouteUrl('admin/system/user/store'));
            form.verify({
                username: [
                    /^[\S]{4,14}$/
                    ,'账号名必须至少4到14字符'
                ],
            });
        });
    </script>
@endsection
