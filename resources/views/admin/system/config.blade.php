@extends('admin.base')

@section('content')
    <div class="layui-card">
        <div class="layui-card-body">
            <div class="layui-tab layui-tab-brief" lay-filter="TabBrief">
                <ul class="layui-tab-title">
                    <li class="layui-this" lay-id="website">网站配置</li>
                    <li lay-id="uploadConfig">上传配置</li>
                    <li lay-id="wechatMiniProgramConfig">小程序配置</li>
                </ul>
                <div class="layui-tab-content" >
                    <div class="layui-tab-item layui-show">
                        <form class="layui-form layui-form-pane" method="post" lay-filter="website">
                            <input type="hidden" name="_method" value="put">
                            <div class="layui-form-item">
                                <label class="layui-form-label">名称</label>
                                <div class="layui-input-block">
                                    <input type="text" name="webname" placeholder="请输入标题" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">标题</label>
                                <div class="layui-input-block">
                                    <input type="text" name="title" placeholder="请输入标题" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">关键词</label>
                                <div class="layui-input-block">
                                    <input type="text" name="keywords" placeholder="请输入关键词" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label class="layui-form-label">描述</label>
                                <div class="layui-input-block">
                                    <input type="text" name="description" placeholder="请输入描述" class="layui-input">
                                </div>
                            </div>
                            <div class="layui-form-item layui-form-text">
                                <label class="layui-form-label">版权</label>
                                <div class="layui-input-block">
                                    <textarea class="layui-textarea" name="copyright" rows="8"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="layui-tab-item">
                        <blockquote class="layui-elem-quote layui-quote-nm">
                            <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大单个上传文件大小为: <span id="upload_max_filesize">--</span></p>
                            <p><i class="layui-icon layui-icon-about"></i> 当前 PHP 环境允许最大 POST 表单大小为: <span id="post_max_size">--</span></p>
                        </blockquote>
                        <form class="layui-form layui-form-pane" method="post" lay-filter="uploadConfig">
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
                                        <div class="layui-input-inline" style="width: 180px;">
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
                                            <div class="layui-input-inline" style="width: 180px;">
                                                <input type="number" name="image_witch" class="layui-input">
                                            </div>
                                            <div class="layui-form-mid">x</div>
                                            <div class="layui-input-inline" style="width: 180px;">
                                                <input type="number" name="image_height" class="layui-input">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="layui-form-item layui-hide image_quality">
                                        <label class="layui-form-label">图像质量</label>
                                        <div class="layui-input-block" style="width: 180px;">
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
                                        <div class="layui-input-inline" style="width: 180px;">
                                            <input type="number" name="file_size" lay-verify="number" class="layui-input">
                                        </div>
                                        <div class="layui-form-mid layui-word-aux">KB 提示：1 M = 1024 KB</div>
                                    </div>
                                </div>
                            </fieldset>
                        </form>
                    </div>

                    <div class="layui-tab-item">
                        <form class="layui-form layui-form-pane" method="post" lay-filter="wechatMiniProgramConfig">
                            <input type="hidden" name="_method" value="put">
                            <div class="layui-form-item" pane="">
                                <label class="layui-form-label">日志级别</label>
                                <div class="layui-input-block">
                                    <input type="radio" name="log_level" value="debug" title="debug" checked>
                                    <input type="radio" name="log_level" value="info" title="info" >
                                    <input type="radio" name="log_level" value="notice" title="notice" >
                                    <input type="radio" name="log_level" value="warning" title="warning" >
                                    <input type="radio" name="log_level" value="error" title="error" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">app_id</label>
                                <div class="layui-input-block">
                                    <input type="text" name="app_id" placeholder="请输入APPID" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">secret</label>
                                <div class="layui-input-block">
                                    <input type="text" name="secret" placeholder="请输入APPSECRET" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">token</label>
                                <div class="layui-input-block">
                                    <input type="text" name="token" placeholder="请输入TOKEN" class="layui-input" >
                                </div>
                            </div>
                            <div class="layui-form-item">
                                <label for="" class="layui-form-label">aes_key</label>
                                <div class="layui-input-block">
                                    <input type="text" name="aes_key" placeholder="请输入消息AESKEY" class="layui-input" >
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="layui-form-item">
                <div class="layui-input-block">
                    <button type="button" class="layui-btn" lay-active="BaseFormSubmit"><i class="layui-icon layui-icon-release"></i> 保 存</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'inputTag'], function () {
            let $ = layui.jquery, form = layui.form, inputTag = layui.inputTag,
                siteKey = 'website', F = {
                init() {
                    let that = this;
                    form.on('switch(compress)', function(data) {
                        if (data.elem.checked) {
                            $(".image_size").addClass('layui-show').removeClass('layui-hide');
                            $(".image_quality").addClass('layui-show').removeClass('layui-hide');
                        }else {
                            $(".image_size").addClass('layui-hide').removeClass('layui-show');
                            $(".image_quality").addClass('layui-hide').removeClass('layui-show');
                        }
                    });
                    layui.element.on('tab(TabBrief)', function(elem) {
                        siteKey = this.getAttribute('lay-id');
                        if (siteKey === 'uploadConfig') {
                            $("#image_type").prevAll().remove();
                            $("#file_type").prevAll().remove();
                        }
                        that.GetData(siteKey);
                    });
                    layui.util.event('lay-active', {
                        BaseFormSubmit: function() {
                            layer.confirm('确定提交么？', function() {
                                let formData = form.val(siteKey);
                                layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                                $.post(AppGlobalMethods.RouteUrl("admin/system/config/"+siteKey+"/update"), formData, function (result) {
                                    result.code === 0 ? layer.msg(result.message, {time: 2000, icon: 6}) : layer.msg(result.message||"系统错误", {time: 3000, icon: 5});
                                });
                            });
                        }
                    });
                    that.GetData(siteKey);
                },
                GetData(siteKey) {
                    $.get(AppGlobalMethods.RouteUrl('admin/system/config/data'), {siteKey}, function (res) {
                        let data = res.data, formData = form.val(siteKey);
                        if (data && siteKey === 'uploadConfig') {
                            $("#upload_max_filesize").text(data.upload_max_filesize||'');
                            $("#post_max_size").text(data.post_max_size||'');
                            if (data.compress === 1) {
                                $(".image_size").addClass('layui-show').removeClass('layui-hide');
                                $(".image_quality").addClass('layui-show').removeClass('layui-hide');
                            }
                            inputTag.render({
                                elem: '#image_type',
                                data: data.image_type,
                                theme: ['fairy-bg-blue'],
                                onChange: function (value) {
                                    $('input[name=image_type]').val(value.join(','));
                                }
                            })
                            inputTag.render({
                                elem: '#file_type',
                                data: data.file_type,
                                theme: ['fairy-bg-blue'],
                                onChange: function (value) {
                                    $('input[name=file_type]').val(value.join(','));
                                }
                            });
                        }
                        form.val(siteKey, Object.assign({}, formData, data));
                    })
                }
            };F.init();
        })
    </script>
@endsection
