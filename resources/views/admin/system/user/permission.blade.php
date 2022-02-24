@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form method="post" class="layui-form" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            <div id="permission"></div>
        </form>
    </div>
@endsection

@section('script')
    <script type="text/javascript">
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form,
                formInit = function () {
                    form.on('checkbox', function (data) {
                        let check = data.elem.checked,checkId = data.elem.id;
                        if (check) {
                            let ids = checkId.split("-");
                            if (ids.length === 3) {
                                $("#" + (ids[0] + '-' + ids[1])).prop("checked", true);
                                $("#" + (ids[0])).prop("checked", true);
                            } else if (ids.length === 2) {
                                $("#" + (ids[0])).prop("checked", true);
                                $("input[id*=" + ids[0] + '-' + ids[1] + "-]").each(function (i, ele) {
                                    $(ele).prop("checked", true);
                                });
                            } else if (ids.length === 1) {
                                $("input[id*=" + ids[0] + "-]").each(function (i, ele) {
                                    $(ele).prop("checked", true);
                                });
                            }
                            return form.render();
                        }
                        //取消选中
                        let ids = checkId.split("-");
                        if (ids.length === 2) {
                            $("input[id*=" + ids[0] + '-' + ids[1] + "-]").each(function (i, ele) {
                                $(ele).prop("checked", false);
                            });
                        } else if (ids.length === 1) {
                            $("input[id*=" + ids[0] + "-]").each(function (i, ele) {
                                $(ele).prop("checked", false);
                            });
                        }
                        form.render();
                    });
                },
                htmlTpl = function (data) {
                    let html = '';
                    if (data.length === 0) {
                        html += '<div style="text-align: center;padding:20px 0;"> 无数据 </div>';
                        return html;
                    }
                    layui.each(data, function(index, first){
                        html += '<dl class="cate-box">'
                            + '<dt>'
                            + '   <div class="cate-first">'
                            + '       <input id="menu'+first.id+'" type="checkbox" name="permissions[]" value="'+first.name+'" title="'+first.display_name+'" lay-skin="primary" '+(first.own||"")+'>'
                            +'    </div>'
                            +'</dt>';
                        if (first._child) {
                            layui.each(first._child, function(index2, second) {
                                html += '<dd>'
                                    +'<div class="cate-second">'
                                    +    '<input id="menu'+first.id+'-'+second.id+'" type="checkbox" name="permissions[]" value="'+second.name+'" title="'+second.display_name+'" lay-skin="primary" '+(second.own||"")+'>'
                                    +'</div>';
                                if(second._child) {
                                    html += '<div class="cate-third">';
                                    layui.each(second._child, function(index3, three) {
                                        html += '<input type="checkbox" id="menu'+first.id+'-'+second.id+'-'+three.id+'" name="permissions[]" value="'+three.name+'" title="'+three.display_name+'" lay-skin="primary" '+(three.own||"")+'>'
                                    })
                                    html += '</div>';
                                }
                                html += '</dd>';
                            })
                        }
                        html += '</dl>';
                    })
                    return html;
                };

            $.get(location.href, {}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', location.href);
                $('#permission').append(htmlTpl(data.permissions));
                form.render();
                formInit();
            })
        });
    </script>
@endsection

