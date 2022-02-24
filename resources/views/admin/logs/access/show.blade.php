  @extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <table class="layui-table access-logs" style="table-layout: fixed;">
            <tbody>
            <tr>
                <td class="set">路由</td>
                <td colspan="3" class="layui-elip path"></td>
            </tr>
            <tr>
                <td class="set">IP地址</td>
                <td class="ip"></td>
                <td class="set">请求方式</td>
                <td class="method"></td>
            </tr>
            <tr>
                <td class="set">请求参数</td>
                <td colspan="3"><a class="layui-btn layui-btn-sm" lay-active="showInputCode">点击查看</a></td>
            </tr>
            <tr>
                <td class="set">Header</td>
                <td colspan="3" class="layui-elip"><a class="layui-btn layui-btn-sm" lay-active="showHeaderCode"">点击查看</a></td>
            </tr>
            <tr>
                <td class="set">IP解析地址</td>
                <td class="ip_address"></td>
                <td class="set">请求时间</td>
                <td class="created_at"></td>
            </tr>
            <tr>
                <td class="set">操作系统</td>
                <td class="platform"></td>
                <td class="set">浏览器</td>
                <td class="browser"></td>
            </tr>
            <tr>
                <td class="set">设备名称</td>
                <td class="device_name"></td>
                <td class="set">语言</td>
                <td class="languages"></td>
            </tr>
            <tr>
                <td class="set">是否机械人</td>
                <td class="isRobot"></td>
                <td class="set">机械人名称</td>
                <td class="robot_name"></td>
            </tr>
            </tbody>
        </table>
    </div>
@endsection

@section('script')
<script>
    layui.use(['index'], function() {
        let $ = layui.jquery, input_code_html = '', header_code_html = '',
            layerOpen = function (content) {
                if (content) {
                    layer.open({
                        type: 1,
                        skin: 'layui-layer-demo',
                        closeBtn: 0,
                        maxHeight: 400,
                        title: false,
                        shadeClose: true,
                        content: content,
                        success (){
                            layui.code();
                        }
                    });
                }
            };
        $.get(location.href, {}, function (res) {
            let data = res.data, obj = $('.access-logs');
            obj.find('.path').text(data.path);
            obj.find('.ip').text(data.ip);
            obj.find('.method').text(data.method);
            obj.find('.ip_address').text(data.ip_address||'--');
            obj.find('.created_at').text(data.created_at);
            obj.find('.platform').html(data.platform+' '+(data.platform_version||''));
            obj.find('.browser').html(data.browser+' '+(data.browser_version||''));
            obj.find('.device_name').text(data.device_name);
            obj.find('.languages').text(data.languages);
            obj.find('.isRobot').text(data.is_robot?'是':'否');
            obj.find('.robot_name').text(data.robot_name||'--');
            input_code_html += '<pre class="layui-code" style="min-width: 200px;">'+AppGlobalMethods.formatJson(data.input)+'</pre>'
            header_code_html += '<pre class="layui-code" style="min-width: 200px;">'+AppGlobalMethods.formatJson(data.header)+'</pre>'
        })
        layui.util.event('lay-active', {
            showInputCode() {
                layerOpen(input_code_html);
            },
            showHeaderCode() {
                layerOpen(header_code_html);
            }
        });
    });
</script>
@endsection