@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.content.category._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery;
            $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/article_category/store'));
        });
    </script>
@endsection