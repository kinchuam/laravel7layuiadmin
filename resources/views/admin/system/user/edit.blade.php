@extends('admin.from')

@section('content')
<div class="layui-card-body">
    <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
        <input type="hidden" name="id">
        <input type="hidden" name="_method" value="put">
        @include('admin.system.user._form')
    </form>
</div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            form.verify({
                username: [
                    /^[\S]{4,14}$/
                    ,'账号名必须至少4到14字符'
                ],
            });
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $("input[name=id]").val(data.id);
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/system/user/'+data.id+'/update'));
                form.val('FormExample', {
                    "username": data.username||'',
                    "display_name": data.display_name||'',
                });
            });
        });
    </script>
@endsection
