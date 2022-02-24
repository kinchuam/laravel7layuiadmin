@extends('admin.base')

@section('content')
    <div class="layui-card attachmentBlade" style="display: none">
        <div class="layui-card-body" pad15="">
            <blockquote class="layui-elem-quote layui-quote-nm">
                <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大单个上传文件大小为: <span id="upload_max_filesize">--</span></p>
                <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大 POST 表单大小为: <span id="post_max_size">--</span></p>
            </blockquote>
            <form class="layui-form layui-form-pane" method="post" lay-filter="BaseForm">
                <input type="hidden" name="_method" value="put">
                <div class="layui-form-item" pane="">
                    <label class="layui-form-label">存储方式</label>
                    <div class="layui-input-block">
                        <input type="radio" name="storage" lay-filter="storage" value="local" title="本地" checked>
                        <input type="radio" name="storage" lay-filter="storage" value="kodo" title="七牛云" >
                        <input type="radio" name="storage" lay-filter="storage" value="cos" title="腾讯云" >
                        <input type="radio" name="storage" lay-filter="storage" value="oss" title="阿里云" >
                    </div>
                </div>

                <fieldset class="layui-elem-field">
                    <legend>图片上传设置</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item" pane="">
                            <label class="layui-form-label">文件后缀</label>
                            <div class="layui-input-block">
                                <div class="fairy-tag-container" style="min-height:auto;">
                                    <input type="text" id="image_type" class="fairy-tag-input" >
                                    <input type="hidden" name="image_type" >
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件大小</label>
                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="number" name="image_size" lay-verify="number" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">KB 提示：1 M = 1024 KB</div>
                        </div>
                        <div class="layui-form-item" pane="">
                            <label class="layui-form-label">开启压缩</label>
                            <div class="layui-input-inline">
                                <input type="checkbox" name="compress" lay-filter="compress" lay-skin="switch" value="1">
                            </div>
                            <div class="layui-form-mid layui-word-aux">只有存储方式为本地才生效</div>
                        </div>
                        <div class="layui-form-item layui-hide image_size">
                            <div class="layui-block">
                                <label class="layui-form-label">图像尺寸</label>
                                <div class="layui-input-inline" style="width: 150px;">
                                    <input type="number" name="image_witch" class="layui-input">
                                </div>
                                <div class="layui-form-mid">x</div>
                                <div class="layui-input-inline" style="width: 150px;">
                                    <input type="number" name="image_height" class="layui-input">
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item layui-hide image_quality">
                            <label class="layui-form-label">图像质量</label>
                            <div class="layui-input-block" style="width: 150px;">
                                <input type="number" min="0" max="100" name="image_quality" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">图像的质量范围从0-100，编码 JPG 格式时才会应用质量。默认值: 90</div>
                        </div>
                    </div>
                </fieldset>

                <fieldset class="layui-elem-field">
                    <legend>附件上传设置</legend>
                    <div class="layui-field-box">
                        <div class="layui-form-item" pane="">
                            <label class="layui-form-label">文件后缀</label>
                            <div class="layui-input-block">
                                <div class="fairy-tag-container" style="min-height:auto;">
                                    <input type="text" id="file_type" class="fairy-tag-input" >
                                    <input type="hidden" name="file_type" >
                                </div>
                            </div>
                        </div>
                        <div class="layui-form-item">
                            <label class="layui-form-label">文件大小</label>
                            <div class="layui-input-inline" style="width: 150px;">
                                <input type="number" name="file_size" lay-verify="number" class="layui-input">
                            </div>
                            <div class="layui-form-mid layui-word-aux">KB 提示：1 M = 1024 KB</div>
                        </div>
                    </div>
                </fieldset>

                <div class="layui-form-item">
                    <div class="layui-input-block">
                        <button type="button" class="layui-btn" lay-filter="BaseFormSubmit"><i class="layui-icon layui-icon-release"></i> 保 存</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'inputTag'], function() {
            let $ = layui.jquery, form = layui.form, inputTag = layui.inputTag, siteKey = 'attachment.set';
            form.on('radio(storage)', function(data) {

            });
            form.on('switch(compress)', function(data) {
                if (data.elem.checked) {
                    $(".image_size").addClass('layui-show').removeClass('layui-hide');
                    $(".image_quality").addClass('layui-show').removeClass('layui-hide');
                }else {
                    $(".image_size").addClass('layui-hide').removeClass('layui-show');
                    $(".image_quality").addClass('layui-hide').removeClass('layui-show');
                }
            });
            $.get(AppGlobalMethods.RouteUrl('admin/config/data'), {siteKey}, function (res) {
                let data = res.data;
                $('.layui-form').attr('action', AppGlobalMethods.RouteUrl("admin/config/attachment/"+siteKey+"/update"));
                form.val('BaseForm', {
                    "storage": data.storage||'local',
                    "image_size": data.image_size||2048,
                    "compress": data.compress == 1,
                    "image_witch": data.image_witch,
                    "image_height": data.image_height,
                    "image_quality": data.image_quality,
                    "file_size": data.file_size||5120,
                });
                if (data.compress == 1) {
                    $(".image_size").addClass('layui-show').removeClass('layui-hide');
                    $(".image_quality").addClass('layui-show').removeClass('layui-hide');
                }
                let image_type = data.image_type?data.image_type.split('|'):[],
                    file_type = data.file_type?data.file_type.split('|'):[];
                inputTag.render({
                    elem: '#image_type',
                    data: image_type,
                    theme: ['fairy-bg-blue'],
                    onChange: function (value) {
                        $('input[name=image_type]').val(value.join('|'));
                    }
                });
                inputTag.render({
                    elem: '#file_type',
                    data: file_type,
                    theme: ['fairy-bg-blue'],
                    onChange: function (value) {
                        $('input[name=file_type]').val(value.join('|'));
                    }
                });
            });
            setTimeout(function() {
                $(".attachmentBlade").fadeIn(380);
            }, 500);
        });
    </script>
@endsection
