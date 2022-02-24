@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.system.permission._from')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','xmSelect'], function () {
            let $ = layui.jquery, xmSelect = layui.xmSelect,
                parent_id = AppGlobalMethods.GetQueryVariable("parent_id"),
                SelectCategory = xmSelect.render({
                    el: '#parent',
                    name: 'parent_id',
                    model: { label: { type: 'text' } },
                    prop: { name: 'display_name', value: 'id', },
                    tips: '上级权限',
                    radio: true,
                    clickClose: true,
                    tree: { show: true, strict: false, },
                });
            $(".layui-form").attr('action', AppGlobalMethods.RouteUrl('admin/system/permission/store'));
            $.get(AppGlobalMethods.RouteUrl('admin/system/permission/data'), {"parent_id": 0, "with_type": 'parent'}, function (res) {
                SelectCategory.update({
                    data: res.data,
                    autoRow: true,
                });
                SelectCategory.setValue([parent_id]);
                SelectCategory.changeExpandedKeys([AppGlobalMethods.GetParentId(res.data, parent_id)])
            });
        });
    </script>
@endsection