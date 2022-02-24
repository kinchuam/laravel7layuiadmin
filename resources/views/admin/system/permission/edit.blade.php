@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            <input type="hidden" name="id" >
            @include('admin.system.permission._from')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','xmSelect'], function () {
            let $ = layui.jquery, form = layui.form, xmSelect = layui.xmSelect,
                SelectCategory = xmSelect.render({
                    el: '#parent',
                    name: 'parent_id',
                    model: { label: { type: 'text' } },
                    prop: { name: 'display_name', value: 'id', },
                    tips: '上级权限',
                    radio: true,
                    clickClose: true,
                    tree: { show: true, strict: false, },
                }),
                GetPermission = function (parent_id = 0) {
                    $.get(AppGlobalMethods.RouteUrl('admin/system/permission/data'), {"parent_id": 0, "with_type": 'parent'}, function (res) {
                        SelectCategory.update({
                            data: res.data,
                            autoRow: true,
                        });
                        console.log(parent_id,123)
                        SelectCategory.setValue([parent_id]);
                        SelectCategory.changeExpandedKeys([AppGlobalMethods.GetParentId(res.data, parent_id)])
                    });
                };

            $.get(location.href, {}, function (res) {
                let data = res.data;
                $("input[name=id]").val(data.id);
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/system/permission/'+data.id+'/update'));
                form.val('FormExample', {
                    "name": data.name||'',
                    "display_name": data.display_name||'',
                    "genre": data.genre||'',
                });
                GetPermission(data.parent_id||0);
            })
        });
    </script>
@endsection