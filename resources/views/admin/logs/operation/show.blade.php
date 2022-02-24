@extends('admin.from')

@section('content')
    <div class="layui-card">
        <pre class="layui-code"></pre>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index'], function() {
            let $ = layui.jquery;
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $(".layui-code").html(AppGlobalMethods.formatJson(data.properties));
                layui.code();
            })
        });
    </script>
@endsection