@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.content.files_group._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery;
            $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/files_group/store'));
        });
    </script>
@endsection