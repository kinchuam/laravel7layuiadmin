@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            <div class="layui-form-item">
                <label for="" class="layui-form-label must">密码</label>
                <div class="layui-input-block" >
                    <input type="password" name="password" placeholder="请输入密码" lay-verify="pass" lay-vertype="tips"  class="layui-input">
                </div>
            </div>
            <div class="layui-form-item">
                <label for="" class="layui-form-label must">确认密码</label>
                <div class="layui-input-block" >
                    <input type="password" name="password_confirmation" lay-verify="pass_confirm" lay-vertype="tips" placeholder="请输入确认密码" class="layui-input">
                </div>
            </div>
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, form = layui.form;
            $('.layui-form').attr('action', location.href);
            form.verify({
                pass: function (value, item) {
                    if (value == '') {
                        return '请输入密码';
                    }
                    if(!new RegExp(/^[\S]{6,14}$/).test(value)){
                        return '密码必须8到14位，且不能出现空格';
                    }
                },
                pass_confirm: function (value, item) {
                    if (value == '') {
                        return '请输入确认密码';
                    }
                    if(!new RegExp(/^[\S]{6,14}$/).test(value)){
                        return '密码必须8到14位，且不能出现空格';
                    }
                },
            });
        });
    </script>
@endsection
