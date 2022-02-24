let AppGlobalMethods  = {

    HtmlClearTags(str) {
        return this.Trim(str.replace(/<i\s*[^>]*>(.*?)<\/i>/ig,""));
    },

    Trim(str) {
        return str.replace(/(^\s*)|(\s*$)/g, "");
    },

    GetMeta(metaName) {
        const metas = document.getElementsByTagName('meta');
        for (let i = 0; i < metas.length; i++) {
            if (metas[i].getAttribute('name') === metaName) {
                return metas[i].getAttribute('content');
            }
        }
        return '';
    },

    GetBlob(url) {
        return new Promise(resolve => {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', url, true);
            xhr.responseType = 'blob';
            xhr.onload = () => {
                if (xhr.status === 200) {
                    resolve(xhr.response);
                }
            };
            xhr.send();
        });
    },

    SaveAs(blob, filename) {
        if (window.navigator.msSaveOrOpenBlob) {
            navigator.msSaveBlob(blob, filename);
            return true;
        }
        const link = document.createElement('a');
        const body = document.querySelector('body');
        link.href = window.URL.createObjectURL(blob);
        link.download = filename;
        // fix Firefox
        link.style.display = 'none';
        body.appendChild(link);
        link.click();
        body.removeChild(link);
        window.URL.revokeObjectURL(link.href);
        return true;
    },

    download(url, filename) {
        let that = this;
        return that.GetBlob(url).then(blob => {
            that.SaveAs(blob, filename);
        });
    },

    formatJson(json) {
        let formatted = '', padIdx = 0, PADDING = '    ';
        if (typeof json !== 'string') {
            json = JSON.stringify(json);
        }
        json = json.replace(/([\{\}])/g, '\r\n$1\r\n')
            .replace(/([\[\]])/g, '\r\n$1\r\n')
            .replace(/(\,)/g, '$1\r\n')
            .replace(/(\r\n\r\n)/g, '\r\n')
            .replace(/\r\n\,/g, ',');
        (json.split('\r\n')).forEach(function (node, index) {
            let indent = 0, padding = '';
            if (node.match(/\{$/) || node.match(/\[$/)) indent = 1;
            else if (node.match(/\}/) || node.match(/\]/))  padIdx = padIdx !== 0 ? --padIdx : padIdx;
            else  indent = 0;
            for (let i = 0; i < padIdx; i++) padding += PADDING;
            formatted += padding + node + '\r\n';
            padIdx += indent;
        });
        return formatted;
    },

    responseText(xhr) {
        let status = xhr.status, responseText = xhr.responseText, message = '不好，有错误';
        switch (status) {
            case 400:
                message = responseText !== '' ? responseText : '失败了';
                break;
            case 401:
                message = responseText !== '' ? responseText : '你没有权限';
                break;
            case 403:
                message =  '你没有权限执行此操作!';
                break;
            case 404:
                message = '你访问的操作不存在';
                break;
            case 406:
                message = '请求格式不正确';
                break;
            case 410:
                message = '你访问的资源已被删除';
                break;
            case 423:
            case 422:
                if (xhr.responseJSON) {
                    let errors = xhr.responseJSON, m = '';
                    for(let index in errors){
                        let item = errors[index];
                        if (item instanceof Object) {
                            for(let i in item){
                                m += item[i] + '<br>';
                            }
                            break;
                        }
                        m += item + '<br>';
                    }
                    message = m;
                }
                break;
            case 429:
                message = '超出访问频率限制';
                break;
            case 500:
                message = '500 INTERNAL SERVER ERROR';
                break;
        }
        return message;
    },

    handleMoney(price) {
        let AmountUnits = ["元", "万元", "亿", "兆", '京', '垓', '杼'], strum = price.toString(), AmountUnit = '';
        AmountUnits.find((item, index) => {
            let newNum = strum;
            if (strum.indexOf('.') !== -1) {
                newNum = strum.substring(0, strum.indexOf('.'))
            }
            if (newNum.length < 5) {
                AmountUnit = item
                return;
            }
            strum = (newNum * 1 / 10000).toString()
        })
        return (strum * 1) + AmountUnit;
    },

    GetQueryVariable(variable, def = '') {
        if (variable) {
            let query = window.location.search.substring(1), vars = query.split("&");
            for (let i=0; i < vars.length; i++) {
                let pair = vars[i].split("=");
                if(pair[0] == variable){
                    return pair[1];
                }
            }
        }
        return def;
    },

    GetParentId(data, parent_id) {
        for (let i in data) {
            if (data[i].id == parent_id) {
                return parent_id;
            }else if (data[i].children.length > 0) {
                for (let ii in data[i].children) {
                    if (data[i].children[ii].id == parent_id) {
                        return data[i].children[ii].parent_id;
                    }
                }
            }
        }
        return 0;
    },

    imgError(image) {
        image.src = this.RouteUrl("static/admin/img/nopic.png");
        image.onerror = null;
    },

    loadImage(src) {
        let that = this;
        return new Promise(function(resolve) {
            let img = new Image();
            img.onload = function() {
                resolve(img);
            }
            img.onerror = function() {
                that.imgError(this);
            }
            img.src = src;
        });
    },

    GetFileSize(size= 0) {
        if (!size) { return "";}
        let num = 1024;
        if (size < num) { return size + "B"; }
        if (size < Math.pow(num, 2)) { return (size / num).toFixed(2) + "K"; }
        if (size < Math.pow(num, 3)) { return (size / Math.pow(num, 2)).toFixed(2) + "M"; }
        if (size < Math.pow(num, 4)) { return (size / Math.pow(num, 3)).toFixed(2) + "G"; }
        return (size / Math.pow(num, 4)).toFixed(2) + "T";
    },

    DoAdmin() {
        let d = document.location, baseUrl = d.origin;
        if (baseUrl.indexOf("http:") !== -1) {
            baseUrl.replace("http:","");
        }else if (baseUrl.indexOf("https:") !== -1) {
            baseUrl.replace("https:","");
        }
        return baseUrl;
    },

    BaseUrl() {
        let d = document.location, baseUrl = this.DoAdmin();
        if (d.pathname.indexOf("public") !== -1) {
            let pathname = d.pathname.match(/.*\/public*/);
            baseUrl += pathname[0];
        }
        return baseUrl;
    },

    StorageUrl(url= '') {
        if (url.indexOf("http") !== -1 || url.indexOf("https") !== -1) {
            return url;
        }
        url = url.indexOf('/') === 0 ? url.substr(1) : url;
        let d = document.location, baseUrl = this.BaseUrl();
        if (url.indexOf('storage') !== -1) {
            return baseUrl + "/" + url;
        }
        if (d.pathname.indexOf("index.php") !== -1) {
            baseUrl += '/index.php';
        }
        return baseUrl + "/storage/" + url;
    },

    RouteUrl(url= '') {
        if (url.indexOf("http") !== -1 || url.indexOf("https") !== -1) {
            return url;
        }
        url = url.indexOf('/') === 0 ? url.substr(1) : url;
        let d = document.location, baseUrl = this.BaseUrl();
        if (d.pathname.indexOf("index.php") !== -1) {
            baseUrl += '/index.php';
        }
        return baseUrl + "/" + url;
    },

    UserPermissions(permission) {
        let localData = sessionStorage.getItem("AdminSystem");
        if (localData) {
            let data = JSON.parse(localData);
            if (permission && data.UserPermissions) {
                return data.UserPermissions.includes(permission);
            }
        }
        return false;
    },
}