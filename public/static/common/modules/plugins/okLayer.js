/**
 * okLayer
 */
layui.define(["index"], function (exports) {
    let $ = layui.jquery, layer = layui.layer;
    let okLayer = {
        /**
         * confirm()函数二次封装
         * @param content
         * @param yesFunction
         */
        confirm: function (content, yesFunction) {
            let options = {skin: okLayer.skinChoose(), icon: 3, title: "提示", anim: okLayer.animChoose()};
            layer.confirm(content, options, yesFunction);
        },
        /**
         * open()函数二次封装,支持在table页面和普通页面打开
         * @param title
         * @param content
         * @param data
         * @param successFunction
         * @param endFunction
         */
        open: function (title, content, data= {}, successFunction, endFunction) {
            let defaults = {full: false, btn: true, width: '55%', height: '55%'};
            data = Object.assign({}, defaults, data);
            let arr = {
                title: title,
                type: 2,
                maxmin: true,
                shade: 0,
                shadeClose: false,
                anim: okLayer.animChoose(5),
                area: [data.width, data.height],
                content: content,
                //zIndex: layer.zIndex,
                //skin: okLayer.skinChoose(),
                success: successFunction,
                end: endFunction
            };
            if (data.id && data.id > 0) {
                arr.id = data.id;
            }
            if (data.btn) {
                arr.btn =  ['保存', '关闭'];
                arr.btnAlign = 'c';
                arr.yes =  function (index, layero) {
                    let iframeWindow = window[layero.find('iframe')[0]['name']],
                        checkSucceed = function (result) {
                            if (result.code === 0 && result.status !== 'error') {
                                let data = result.data,
                                    SucceedMsg = function (message) {
                                        layer.msg(message||"系统错误", {time: 2000, icon: 6});
                                        layer.close(index)
                                    };
                                if (data) {
                                    if (data.LocalReload) {
                                        return location.reload();
                                    }else if (data.TableRefresh){
                                        let table = layui.table, treeTable = layui.treeTable;
                                        if (treeTable) {
                                            data.fromData ? treeTable.reload('dataTable', {where: data.fromData}) : treeTable.reload('dataTable');
                                        }else if (table) {
                                            data.fromData ? table.reload('dataTable', {where: data.fromData}) : table.reload('dataTable');
                                        }
                                        return SucceedMsg(result.message);
                                    }
                                }
                                return SucceedMsg(result.message);
                            }
                            return layer.msg(result.message, {time: 3000, icon: 5});
                        };
                    layer.confirm('确定提交么？', function(rr) {
                        layer.close(rr);
                        iframeWindow.layui.form.submit('FormExample', function(data) {
                            layer.msg('正在请求...', { icon: 16, shade: 0.01, time:false });
                            $.post(data.form.action, data.field, function (result) {
                                checkSucceed(result);
                            });
                        });
                    });
                };
            }
            let index = layer.open(arr);
            if (data.full){
                layer.full(index);
            }
        },
        /**
         * msg()函数二次封装
         */
        // msg弹窗默认消失时间
        time: 1000,
        // 绿色勾
        greenTickMsg: function (content, callbackFunction) {
            let options = {icon: 1, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 红色叉
        redCrossMsg: function (content, callbackFunction) {
            let options = {icon: 2, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 黄色问号
        yellowQuestionMsg: function (content, callbackFunction) {
            let options = {icon: 3, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 灰色锁
        grayLockMsg: function (content, callbackFunction) {
            let options = {icon: 4, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 红色哭脸
        redCryMsg: function (content, callbackFunction) {
            let options = {icon: 5, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 绿色笑脸
        greenLaughMsg: function (content, callbackFunction) {
            let options = {icon: 6, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        // 黄色感叹号
        yellowSighMsg: function (content, callbackFunction) {
            let options = {icon: 7, time: okLayer.time, anim: okLayer.animChoose()};
            layer.msg(content, options, callbackFunction);
        },
        /**
         * 皮肤选择
         * @returns {string}
         */
        skinChoose: function (kin) {
            let storage = window.localStorage;
            let skin = kin || storage.getItem("skin");
            if (skin == 1) {
                // 灰白色
                return "";
            } else if (skin == 2) {
                // 墨绿色
                return "layui-layer-molv";
            } else if (skin == 3) {
                // 蓝色
                return "layui-layer-lan";
            } else if (!skin || skin == 4) {
                // 随机颜色
                const skinArray = ["", "layui-layer-molv", "layui-layer-lan"];
                return skinArray[Math.floor(Math.random() * skinArray.length)];
            }
        },
        /**
         * 动画选择
         * @returns {number}
         */
        animChoose: function (nim) {
            let storage = window.localStorage;
            let anim = nim || storage.getItem("anim");
            let animArray = ["0", "1", "2", "3", "4", "5", "6"];
            if (animArray.indexOf(anim) > -1) {
                // 用户选择的动画
                return parseInt(anim);
            } else if (!anim || anim == 7) {
                // 随机动画
                return Math.floor(Math.random() * animArray.length);
            }
        }
    }
    exports("okLayer", okLayer);
});
