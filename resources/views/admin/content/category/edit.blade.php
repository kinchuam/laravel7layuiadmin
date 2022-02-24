@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            @include('admin.content.category._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/article_category/'+data.id+'/update'));
                form.val('FormExample', {
                    "sort": data.sort || 0,
                    "name": data.name || '',
                    "status": data.status || 0,
                });
            });
        });
    </script>
@endsection
