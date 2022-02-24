@extends('admin.from')

@section('content')
    <form class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id">
            @include('admin.system.role._form')
        </form>
    </div>
@endsection
 
@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $("input[name=id]").val(data.id);
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/system/role/'+data.id+'/update'));
                form.val('FormExample', {
                    "name": data.name||'',
                    "display_name": data.display_name||''
                });
            })
        })
    </script>
@endsection