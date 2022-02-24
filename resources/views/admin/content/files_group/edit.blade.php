@extends('admin.from')

@section('content')
<div class="layui-card-body">
    <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
        <input type="hidden" name="_method" value="put">
        @include('admin.content.files_group._form')
    </form>
</div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/files_group/update?id='+data.id));
                form.val('FormExample', {
                    "group_name": data.name || '',
                    "sort": data.sort || 0,
                });
            });
        });
    </script>
@endsection
