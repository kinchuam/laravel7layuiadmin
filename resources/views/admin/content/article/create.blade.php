@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            @include('admin.content.article._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index','fileLibrary','tinymce'],function () {
            let $ = layui.jquery, fileLibrary = layui.fileLibrary, tinymce = layui.tinymce, form = layui.form,
                I = {
                    init() {
                        $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/contents/articles/store'));
                        fileLibrary.UploadFileTpl({
                            elem: '#thumb',
                            name: "thumb"
                        });
                        tinymce.render({
                            elem: "#content"
                            , height: 500
                            , relative_urls : false
                            , convert_urls : false
                            , content_style: "img {max-width:100%;}"
                            , setup: function(editor) {
                                editor.on('change', function(){ editor.save(); });
                            }
                            , urlconverter_callback: function (url, node, onSave, name) {
                                if (node === 'img' && url.startsWith('blob:')) {
                                    tinymce.activeEditor && tinymce.activeEditor.uploadImages()
                                }
                                return url;
                            }
                            , images_upload_handler : function (blobInfo, succFun, failFun) {
                                let formData = new FormData();
                                formData.append('target', 'iFile');
                                formData.append('iFile', blobInfo.blob());
                                $.ajax({
                                    url: AppGlobalMethods.RouteUrl('admin/fileUpload'),
                                    dataType: 'json',
                                    type: 'POST',
                                    data: formData,
                                    processData: false,
                                    contentType: false,
                                    success: function (res) {
                                        if (res.code === 0) {
                                            return succFun(res.data.file_url);
                                        }
                                        failFun(res.message);
                                    },
                                    error: function (res) {
                                        failFun("网络错误：" + res.status);
                                    }
                                });
                            }
                        });
                        this.GetCategory();
                    },
                    GetCategory() {
                        $.get(AppGlobalMethods.RouteUrl('admin/content/articles/category'), {}, function (res) {
                            let data = res.data;
                            layui.each(data, function (index, item) {
                                $('select[name=category_id]').append(new Option(item.title, item.id));
                            });
                            form.val('FormExample', {
                                "category_id": AppGlobalMethods.GetQueryVariable("category_id", 0),
                            });
                            form.render("select");
                        });
                    }
                };I.init();
        });
    </script>

@endsection
