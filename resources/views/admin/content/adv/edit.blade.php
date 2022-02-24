@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane"  method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            @include('admin.content.adv._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','fileLibrary'], function () {
            let $ = layui.jquery, form = layui.form, fileLibrary = layui.fileLibrary;
            $('body img').error(function() {
                console.log("图片加载失败");
            });
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/kv/'+data.id+'/update'));
                fileLibrary.UploadFileTpl({
                    elem: '#thumb',
                    name: 'thumb',
                    value: data.thumb
                });
                form.val('FormExample', {
                    "sort": data.sort || 0,
                    "name": data.name || '',
                    "url": data.url || '',
                    "status": data.status || 0,
                });
            });

        });
    </script>
@endsection
