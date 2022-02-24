@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <form class="layui-form layui-form-pane" method="post" lay-filter="FormExample">
            <input type="hidden" name="_method" value="put">
            @include('admin.content.article._form')
        </form>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index', 'fileLibrary', 'tinymce'],function () {
            let $ = layui.jquery, form = layui.form, fileLibrary = layui.fileLibrary, tinymce = layui.tinymce,
                I = {
                    init() {
                        let edit = tinymce.render({
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
                        $.get(location.href, {}, function (res) {
                            let data = res.data;
                            $('.layui-form').attr('action', AppGlobalMethods.RouteUrl('admin/content/articles/'+data.id+'/update'));
                            form.val('FormExample', {
                                "sort": data.sort||0,
                                "category_id": data.category_id||0,
                                "title": data.title||'',
                                "desc": data.desc||'',
                                "status": data.status||0,
                                "url": data.url||0,
                            });
                            edit.setContent(data.content||'')
                            fileLibrary.UploadFileTpl({
                                elem: '#thumb',
                                name: "thumb",
                                value: data.thumb||''
                            });
                        });
                        this.GetCategory();
                    },
                    GetCategory() {
                        $.get(AppGlobalMethods.RouteUrl('admin/content/articles/category'), {}, function (res) {
                            let data = res.data;
                            layui.each(data, function (index, item) {
                                $('select[name=category_id]').append(new Option(item.title, item.id));
                            });
                            form.render("select");
                        });
                    }
                };I.init();
        });
    </script>

@endsection
