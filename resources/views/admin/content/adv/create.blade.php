@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.content.adv._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','fileLibrary'], function () {
            let $ = layui.jquery, fileLibrary = layui.fileLibrary;
            $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/kv/store'));
            fileLibrary.UploadFileTpl({
                elem: '#thumb',
                name: 'thumb',
            });
        });
    </script>
@endsection
