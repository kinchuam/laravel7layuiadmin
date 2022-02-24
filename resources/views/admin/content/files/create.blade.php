@extends('admin.from')

@section('content')
    <div class="layui-card-body">
        <div class="layui-upload">
            <button type="button" class="layui-btn layui-btn-normal" id="testList">选择多文件</button>
            <div class="layui-upload-list">
                <table class="layui-table">
                    <thead>
                    <tr><th>文件名</th>
                        <th>大小</th>
                        <th>上传进度</th>
                        <th>状态</th>
                        <th>操作</th>
                    </tr></thead>
                    <tbody id="demoList"></tbody>
                </table>
            </div>
            <button type="button" class="layui-btn" id="testListAction"><i class="layui-icon layui-icon-release"></i> 开始上传</button>
        </div>
    </div>
@endsection

@section('script')
    <script>
        layui.use(['index',], function () {
            let $ = layui.jquery, element = layui.element, upload = layui.upload;
            //多文件列表上传
            let demoListView = $('#demoList')
            let uploadListIns = upload.render({
                elem: '#testList'
                , url: AppGlobalMethods.RouteUrl('admin/fileUpload')
                , field: 'iFile'
                , accept: 'file'
                , multiple: true
                , auto: false
                , bindAction: '#testListAction'
                , progress: function(n, e, i, j) {
                    element.progress('progress_' + j , n + '%');
                }
                , choose: function (obj) {
                    let files = this.files = obj.pushFile();
                    //读取本地文件
                    obj.preview(function (index, file, result) {
                        let tr = $(['<tr id="upload-' + index + '">'
                            , '<td>' + file.name + '</td>'
                            , '<td>' + (file.size / 1014).toFixed(1) + 'kb</td>'
                            , '<td><div class="layui-progress layui-progress-big" lay-showpercent="true" lay-filter="progress_'+index+'" ><div class="layui-progress-bar" lay-percent="20%"><span class="layui-progress-text">0%</span></div></div></td>'
                            , '<td>等待上传</td>'
                            , '<td>'
                            , '<div class="layui-btn-group">'
                            , '<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                            , '<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete"><i class="layui-icon layui-icon-delete"></i></button>'
                            , '</div>'
                            , '</td>'
                            , '</tr>'].join(''));
                        tr.find('.demo-reload').on('click', function () {
                            obj.upload(index, file);
                        });
                        tr.find('.demo-delete').on('click', function () {
                            delete files[index];
                            tr.remove();
                            uploadListIns.config.elem.next()[0].value = '';
                        });
                        demoListView.append(tr);
                    });
                }
                , before: function (obj) {
                    this.data = {'filetype': 'image', "_token": $('meta[name="csrf-token"]').attr('content')};
                }
                , done: function (res, index, upload) {
                    if (res.code == 0) {
                        let tr = demoListView.find('tr#upload-' + index), tds = tr.children();
                        tds.eq(3).html('<span style="color: #5FB878;">上传成功</span>');
                        tds.eq(4).html('');
                        //parent.layui.table.reload('dataTable');
                        return delete this.files[index];
                    }
                    this.error(index, upload,res);
                }
                , error: function (index, upload,res) {
                    let tr = demoListView.find('tr#upload-' + index), tds = tr.children();
                    tds.eq(3).html('<span style="color: #FF5722;">上传失败：'+res.message+'</span>');
                    tds.eq(4).find('.demo-reload').removeClass('layui-hide');
                }
            });
        })
    </script>
@endsection