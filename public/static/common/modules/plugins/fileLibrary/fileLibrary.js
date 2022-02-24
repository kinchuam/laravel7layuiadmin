/**
 *
 */
layui.define(["index"], function (exports) {
    let $ = layui.jquery, layer = layui.layer,
        choseImg = layui.cache.base+'plugins/fileLibrary/img/chose.png',
        isParent = window.location !== parent.location,
        pageLimit = 12,
        defaults = {
            type: 'image',
            csrfToken: $('meta[name="csrf-token"]').attr('content')
        },
        layUiObj = function () {
            return isParent ? parent.layui : layui;
        },
        layerObj = function () {
            return layUiObj().layer;
        },
        Tpl = {
            library() {
                return '<div class="file-library">\n' +
                    '           <div id="file-library" class="layui-layer-content">\n' +
                    '                <div class="file-group layui-panel">\n' +
                    '                </div>\n' +
                    '                <div class="file-list">\n' +
                    '                    <div class="layui-card-body">\n' +
                    '                        <div class="layui-btn-group">\n' +
                    '                            <button type="button" class="child-file-group layui-btn layui-btn-sm layui-btn-normal" >\n' +
                    '                                移动至 <span class="layui-icon layui-icon-triangle-d" ></span>\n' +
                    '                            </button>\n' +
                    '                            <button type="button" class="file-delete layui-btn layui-btn-sm layui-btn-danger" >\n' +
                    '                                <i class="layui-icon layui-icon-delete"></i> 删除\n' +
                    '                            </button>\n' +
                    '                        </div>\n' +
                    '                        <div class="layui-btn-group" style="float:right;">\n' +
                    '                            <button type="button" class="layui-btn layui-btn-sm layui-btn-normal j-upload">\n' +
                    '                                <i class="layui-icon layui-icon-add-circle-fine"></i> 上传\n' +
                    '                            </button>\n' +
                    '                            <button type="button" class="layui-btn layui-btn-sm j-upload-network">\n' +
                    '                                <i class="layui-icon layui-icon-add-circle-fine"></i> 网络图片\n' +
                    '                            </button>\n' +
                    '                        </div>\n' +
                    '                    </div>\n' +
                    '                    <div id="file-list-body" class="v-box-body">\n' +
                    '                                 <ul class="file-list-item"> </ul>\n' +
                    '                         <div id="imagepage"></div>\n' +
                    '                    </div>\n' +
                    '                </div>\n' +
                    '             </div>\n' +
                    '          </div>';
            },
            fileList(data) {
                let html = '';
                layui.each(data, function(index, item) {
                    html += '<li class="ng-scope" title="'+item.filename+'" data-file-id="'+item.id+'" data-file-path="'+item.path+'">\n' +
                        '          <div class="img-cover" style="background-image: url('+item.file_url+')"> </div>\n' +
                        '          <p class="file-name layui-elip">'+item.filename+'</p>\n' +
                        '          <div class="select-mask"> <img src="'+choseImg+'" alt=""> </div>\n' +
                        '    </li>';
                })
                return html;
            },
            fileListItem(data) {
                return '<li class="ng-scope" title="{{d.file_name}}" data-file-id="'+data.file_id+'" data-file-path="'+data.path+'">\n' +
                    '       <div class="img-cover" style="background-image: url('+data.file_url+')"> </div>\n' +
                    '       <p class="file-name layui-elip">'+data.file_name+'</p>\n' +
                    '       <div class="select-mask"> <img src="'+choseImg+'" alt=""> </div>\n' +
                    '   </li>'
            },
            groupList(data) {
                let html = '<ul class="nav-new layui-menu">\n' +
                    '           <li class="ng-scope layui-menu-item-checked" data-group-id="all">\n' +
                    '                 <div class="layui-menu-body-title" >全部</div>\n' +
                    '           </li>\n' +
                    '           <li class="ng-scope" data-group-id="0">\n' +
                    '               <div class="layui-menu-body-title" >未分组</div>\n' +
                    '            </li>\n';
                layui.each(data, function(index, item) {
                    html += '<li class="ng-scope" data-group-id="'+item.id+'">\n' +
                    '         <div class="group-edit"> <i class="layui-icon layui-icon-edit"></i></div>\n' +
                    '         <div class="group-name layui-elip layui-menu-body-title">'+item.name+'</div>\n' +
                    '         <div class="group-delete"><i class="layui-icon layui-icon-close"></i></div>\n' +
                    '      </li>\n';
                })
                html += '</ul>\n' +
                    '<a class="group-add layui-btn layui-btn-sm" >新增分组</a>\n';
                return html;
            },
            groupItem(data) {
                return '<li class="ng-scope" data-group-id="'+data.group_id+'">\n' +
                    '        <div class="group-edit"> <i class="layui-icon layui-icon-edit"></i></div>\n' +
                    '        <div class="group-name layui-elip layui-menu-body-title">'+data.group_name+'</div>\n' +
                    '        <div class="group-delete"><i class="layui-icon layui-icon-close"></i></div>\n' +
                    '    </li>';
            }
        };
    let fileLibrary = {
        multiImage(options) {
            let elem = options.elem, name = options.name, multiple = options.multiple, limit = options.limit;
            let that = this,
                obj = {
                    type: 1
                    , title: '图片库'
                    , area:  ['850px','584px']
                    , offset: 'auto'
                    , anim: 1
                    , closeBtn: 1
                    , shade: 0.3
                    , shadeClose: true
                    , btn: ['确定', '取消']
                    , btnAlign: 'c'
                    , scrollbar: false
                    , resize: false
                    , content: Tpl.library()
                    , success: function (layero, index) {
                        fileLibrary.render(layero, layui.sessionData('AdminSystem').fileLibrary);
                        return layUiObj().link(layui.cache.base+'plugins/fileLibrary/fileLibrary.css');
                    }
                    , yes: function (index, layero) {
                        let $imagesList = $(elem).next('.input-group').find('.layui-upload-box'), data = fileLibrary.getSelFiles(layero);
                        // 新增图片列表
                        if (data.length <= 0) {
                            return layerObj().msg('请选择图片');
                        }
                        if (limit > 0 && (parseInt($imagesList.find('li').length) + data.length) > limit) {
                            return layerObj().msg('图片数量不能大于' + limit + '张', {anim: 6});
                        }
                        let list = multiple ? data : [data[0]];
                        that.SetValue({elem: options.elemObj, name, multiple}, list);
                        // 渲染html
                        if (multiple) {
                            return layerObj().close(index);
                        }
                        $(elem).prev().val(data[0].file_path);
                        return layerObj().close(index);
                    }
                };
            return layerObj().open(obj);
        },

        UploadFileTpl(options) {
            options = $.extend({
                elem: '.openThumb',
                name: 'thumb',
                multiple: false,
                limit: 5,
            }, options);
            let that = this, elem = $(options.elem), htmlTpl;
            if (options.multiple) {
                htmlTpl = '<button type="button" class="layui-btn uploadBtn">多图片上传</button>\n' +
                    '      <blockquote class="input-group layui-elem-quote layui-quote-nm" style="margin-top: 10px;">\n' +
                    '            <ul class="layui-clear layui-upload-box"></ul>\n' +
                    '      </blockquote>';
            }else {
                htmlTpl = '<input type="text" class="layui-input" name="'+options.name+'" style="width: 80%;float: left;">\n' +
                    '      <button type="button" class="layui-btn uploadBtn">上传图片</button>\n' +
                    '      <div class="input-group">\n' +
                    '           <ul class="layui-upload-box"></ul>\n' +
                    '      </div>';
            }
            $(elem).append(htmlTpl);
            $(elem).find(".uploadBtn").on('click', function () {
                that.multiImage({
                    elem: this,
                    name: options.name,
                    multiple: options.multiple,
                    limit: options.limit,
                    elemObj: options.elem,
                });
            });
            if (options.value) {
                options.value = options.multiple ? JSON.parse(options.value) : [options.value];
            }
            that.SetValue(options, options.value);
            return options;
        },

        SetValue(options, value) {
            if (value) {
                let that = this, uploadTpl = '';
                if (!options.multiple) {
                    $(options.elem).find("input[name="+options.name+"]").val(value);
                }
                layui.each(value, function (index, item) {
                    let st_url = item.file_url||AppGlobalMethods.StorageUrl(item);
                    uploadTpl += '<li> <img lay-src="'+st_url+'" alt=""> <i class="layui-icon layui-icon-delete icon-delete" id="picDelBtn"></i>';
                    if (options.multiple) {
                        uploadTpl += '<input type="hidden" name="'+name+'[]" value="'+item+'">';
                    }
                    uploadTpl += '</li>';
                })
                let upload_box = $(options.elem).find('.input-group .layui-upload-box');
                upload_box.append(uploadTpl);
                that.lazyImg(upload_box);
                $(options.elem).find('img').on('click', function(){
                    layer.photos({
                        photos: { title: "查看", data: [{ src: $(this).attr('src') }] },
                        shade: .01,
                        closeBtn: 1,
                        anim: 5
                    });
                });
                $(options.elem).find("ul li #picDelBtn").on('click', function () {
                    that.delImage(this);
                });
            }
        },

        lazyImg(that) {
            that.find('img').each(function () {
                let Img = $(this);
                Img.attr('src', AppGlobalMethods.RouteUrl("static/admin/img/loading.gif"));
                AppGlobalMethods.loadImage(Img.attr('lay-src')).then(img => {
                    Img.attr('src', img.src).removeAttr("lay-src");
                });
            })
        },

        delImage(that) {
            $(that).parents(".layui-upload").find("input").val("");
            $(that).parent().remove();
        },

        render(elem, options) {
            options = $.extend({}, defaults, options);
            let $element = elem.find(".file-library"),
                annex = {
                    init() {
                        // 注册列表事件
                        this.renderFileList();
                        // 注册文件点击选中事件
                        this.selectFilesEvent();
                        // 注册分类切换事件
                        this.switchClassEvent();
                        // 注册分组事件
                        this.getGroupEvent();
                        // 新增分组事件
                        this.addGroupEvent();
                        // 编辑分组事件
                        this.editGroupEvent();
                        // 删除分组事件
                        this.deleteGroupEvent();
                        // 注册文件删除事件
                        this.deleteFilesEvent();
                        // 注册文件上传事件
                        this.uploadImagesEvent();
                        // 注册网络图片上传事件
                        this.uploadNetworkImagesEvent();
                    },
                    renderFileList(page) {
                        annex.getJsonData({
                            type: options.type,
                            group_id: annex.getCurrentGroupId(),
                            page: page||1,
                        }, function (data) {
                            $element.find('#file-list-body .file-list-item').html(Tpl.fileList(data));
                        });
                    },
                    selectFilesEvent() {
                        // 绑定文件选中事件
                        $element.find('#file-list-body').on('click', '.file-list-item li', function () {
                            $(this).toggleClass('active');
                        });
                    },
                    switchClassEvent() {
                        // 注册分类切换事件
                        $element.find('.file-group').on('click', 'li .layui-menu-body-title', function () {
                            let $this = $(this);
                            // 切换选中状态
                            $this.parent().addClass('active').siblings('.active').removeClass('active');
                            // 重新渲染文件列表
                            annex.renderFileList();
                        });
                    },
                    getGroupEvent() {
                        $.get(options.GetGroup, {is_all:1}, function (result) {
                            options.GroupList = result.data;
                            $element.find('.file-group').html(Tpl.groupList(options.GroupList));
                            // 注册分组下拉选择组件
                            annex.selectDropdown();
                        });
                    },
                    addGroupEvent() {
                        $element.on('click', '.group-add', function () {
                            let $groupList = $(this).prev();
                            layerObj().prompt({title: '请输入分组名称'}, function (value, index) {
                                layerObj().msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                                $.post(options.AddGroup, {
                                    group_name: value, group_type: options.type
                                }, function (result) {
                                    if (result.code == 0) {
                                        $groupList.append(Tpl.groupItem(result.data));
                                        options.GroupList.push({id: result.data.group_id, name: result.data.group_name});
                                    }
                                    layerObj().msg(result.message);
                                });
                                layerObj().close(index);
                            });
                        });
                    },
                    editGroupEvent() {
                        $element.find('.file-group').on('click', '.group-edit', function () {
                            let $li = $(this).parent() , group_id = $li.data('group-id');
                            layerObj().prompt({title: '修改分组名称', value: $li.find('.group-name').text()}, function (value, index) {
                                if (value != $li.find('.group-name').text()) {
                                    layerObj().msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                                    $.post(options.EditGroup, {
                                        id: group_id, group_name: value, _method: 'put'
                                    }, function (result) {
                                        if (result.code == 0) {
                                            $li.attr('title', value).find('.group-name').text(value);
                                            layui.each(options.GroupList, function (i, item) {
                                                if (item.id == group_id) {
                                                    options.GroupList[i].name = value;
                                                    return true;
                                                }
                                            });
                                        }
                                        layerObj().msg(result.message);
                                    });
                                }
                                layerObj().close(index);
                            });
                        });
                    },
                    deleteGroupEvent() {
                        $element.find('.file-group').on('click', '.group-delete', function () {
                            let $li = $(this).parent(), group_id = $li.data('group-id');
                            layerObj().confirm('确定删除该分组吗？', {title: '友情提示'}, function (index) {
                                layerObj().msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                                $.post(options.DeleteGroup, { _method:'delete', ids: [group_id] }, function (result) {
                                    if (result.code == 0) {
                                        $li.remove();
                                        layui.each(options.GroupList, function (index, item) {
                                            if (item.id == group_id) {
                                                options.GroupList.splice(index, 1);
                                                return true;
                                            }
                                        });
                                    }
                                    layerObj().msg(result.message);
                                });
                                layerObj().close(index);
                            });
                        });
                    },
                    deleteFilesEvent() {
                        $element.on('click', '.file-delete', function () {
                            let fileIds = annex.getSelectedFileIds();
                            if (fileIds.length === 0) {
                                layerObj().msg('您还没有选择任何文件~', {offset: 't', anim: 6});return;
                            }
                            layerObj().confirm('确定删除选中的文件吗？', {title: '友情提示'}, function (index) {
                                layerObj().msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                                $.post(options.DeleteFiles, {
                                    "_method": "delete", ids: fileIds
                                }, function (result) {
                                    if (result.code == 0) {
                                        annex.renderFileList();
                                    }
                                    layerObj().msg(result.message);
                                });
                                layerObj().close(index);
                            });
                        });
                    },
                    uploadImagesEvent() {
                        layUiObj().upload.render({
                            elem: '.j-upload'
                            , url: options.FileUpload
                            , acceptMime: 'image/*'
                            , field: 'iFile'
                            , data: {"filetype": options.type , "_token": options.csrfToken}
                            , multiple: true
                            , before: function (obj) {
                                this.data = {"group_id": annex.getCurrentGroupId()};
                                layerObj().msg('正在上传...', { icon: 16, shade: 0.01, time:false });
                            }
                            , done: function (res) {
                                if (res.code == 0) {
                                    annex.renderFileList();
                                }
                                layerObj().msg(res.message);
                            }
                        });
                    },
                    uploadNetworkImagesEvent() {
                        $element.on('click', '.j-upload-network', function () {
                            layerObj().prompt({title: '输入图片地址', formType: 2}, function(value, index, elem){
                                layerObj().msg('正在上传...', { icon: 16, shade: 0.01, time:false });
                                $.post(options.FileUpload, { upload_type:'NetworkToLocal', url:value, group_id: annex.getCurrentGroupId()}, function (res) {
                                    if (res.code == 0) {
                                        annex.renderFileList();
                                    }
                                    layerObj().msg(res.message);
                                });
                                layerObj().close(index);
                            });
                        });
                    },

                    getCurrentGroupId() {
                        return $element.find('.file-group > ul > li.active').data('group-id');
                    },
                    selectDropdown() {
                        layUiObj().dropdown.render({
                            elem: '.child-file-group',
                            data: options.GroupList,
                            style: 'overflow:auto;overflow-x: hidden; max-height: 243px;',
                            templet: '<div>{{d.name}}</div>',
                            click: function(obj) {
                                let fileIds = annex.getSelectedFileIds();
                                if (fileIds.length <= 0) {
                                    layerObj().msg('您还没有选择任何文件~', {offset: 't', anim: 6});return;
                                }
                                layerObj().confirm('确定移动选中的文件吗？', {title: '友情提示'}, function (index) {
                                    layerObj().msg('正在处理请求...', { icon: 16, shade: 0.01, time:false });
                                    $.post(options.MoveFiles, { group_id: obj.id, fileIds }, function (result) {
                                        if (result.code == 0) {
                                            annex.renderFileList();
                                        }
                                        layerObj().msg(result.message);
                                    });
                                    layerObj().close(index);
                                });
                            }
                        });
                    },
                    fileListPage(data) {
                        layUiObj().laypage.render({
                            limit: pageLimit,elem: 'imagepage',
                            count: data.data.count,
                            curr: data.page,
                            jump: function(obj, first) {
                                if(!first) {
                                    annex.renderFileList(obj.curr);
                                }
                            }
                        });
                    },
                    getJsonData(params, success) {
                        let loadIndex = layerObj().load(2);
                        typeof params === 'function' && (success = params);
                        // 获取文件库列表
                        params.limit = pageLimit;
                        $.get(options.FileList, params, function (result) {
                            layerObj().close(loadIndex);
                            if (result.code != 0) {
                                layerObj().msg(result.message, {anim: 6});
                                return;
                            }
                            typeof success === 'function' && success(result.data);
                            // 注册文件列表分页事件
                            result.page = params.page;
                            annex.fileListPage(result);
                        })
                    },
                    getSelectedFiles() {
                        let selectedList = [];
                        $element.find('.file-list-item > li.active').each(function (index) {
                            selectedList[index] = { file_id: $(this).data('file-id'), file_path: $(this).data('file-path') };
                        });
                        return selectedList;
                    },
                    getSelectedFileIds() {
                        let fileList = annex.getSelectedFiles(), data = [];
                        fileList.forEach(function (item) {
                            data.push(item.file_id);
                        });
                        return data;
                    },
                };
            annex.init();
        },

        getSelFiles: function ($element) {
            let selectedList = [];
            $element.find('.file-list-item > li.active').each(function (index) {
                let $this = $(this), _bk = $this.find('.img-cover').css("background-image");
                selectedList[index] = {
                    file_id: $this.data('file-id'),
                    file_path: $this.data('file-path'),
                    file_url: _bk.split("\"")[1],
                };
            });
            return selectedList;
        }
    }
    exports("fileLibrary", fileLibrary);
});
