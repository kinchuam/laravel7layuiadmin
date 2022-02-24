@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-header">配置信息</div>
        <div class="layui-row layui-col-space15">
            <div class="layui-col-sm7">
                <fieldset class="layui-elem-field ">
                    <legend><a name="default">环境</a></legend>
                    <div class="layui-field-box">
                        <table class="layui-table" lay-skin="line">
                            <tbody id="envs"></tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
            <div class="layui-col-sm5">
                <fieldset class="layui-elem-field ">
                    <legend><a name="default">依赖</a></legend>
                    <div class="layui-field-box">
                        <table class="layui-table" lay-skin="line">
                            <tbody id="dependencies"></tbody>
                        </table>
                    </div>
                </fieldset>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index'],function () {
            let $ = layui.jquery;
            $.get(location.href, {}, function (res) {
                let data = res.data, envs = data.envs || [], dependencies = data.dependencies || [];
                let envs_html = '', dependencies_html = '';
                $.each(envs, function (index, item) {
                     envs_html += '<tr> <td>'+item.name+'</td> <td> <p '+ (item.color ? 'style="color:'+item.color+';"' : '') +'>'+ item.value +'</p> </td> </tr>';
                })
                $("#envs").html(envs_html);
                $.each(dependencies, function (index, item) {
                    dependencies_html += '<tr> <td>'+index+'</td> <td><span class="layui-btn layui-bg-blue layui-btn-xs">'+item+'</span></td> </tr>';
                })
                $("#dependencies").html(dependencies_html);
            })
        })
    </script>
@endsection

