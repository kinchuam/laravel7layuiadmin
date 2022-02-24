@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            <div class="layui-form-item">
                <label for="" class="layui-form-label">角色</label>
                <div class="layui-input-block" id="roles"></div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form,
                htmlTpl = function (data) {
                    let html = '';
                    if(data.length === 0) {
                        return html + '<div class="layui-form-mid layui-word-aux">还没有角色</div>';
                    }
                    layui.each(data, function(index, role) {
                        html += '<input type="checkbox" name="roles[]" value="'+role.name+'" title="'+role.display_name+'" '+(role.own?'checked':"")+'>'
                    })
                    return html;
                };
            $.get(location.href, {}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', location.href);
                $('#roles').append(htmlTpl(data.roles));
                form.render('checkbox');
            });
        });
    </script>
@endsection