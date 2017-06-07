var utils = {
    getUrlParam:function(name){
         return decodeURIComponent((new RegExp('[?|&]'+name+'='+'([^&;]+?)(&|#|;|$)').exec(location.href)||[,""])[1].replace(/\+/g,'%20'))||null;
    }
}

Vue.prototype.$utils = utils;

Vue.filter('time',
    function(value) {
        var dateTime = new Date(parseInt(value *1000));
        var Y = dateTime.getFullYear(),
            m = dateTime.getMonth() + 1,
            d = dateTime.getDate(),
            H = dateTime.getHours(),
            i = dateTime.getMinutes(),
            s = dateTime.getSeconds();
            if(m < 10) {
                m = '0' + m;
            }
            if(d < 10) {
                d = '0' + d;
            }
            if(H < 10) {
                H = '0' + H;
            }
            if(i < 10) {
                i = '0' + i;
            }
            if(s < 10) {
                s = '0' + s;
            }
        var t = Y + '-' + m + '-' + d + ' ' + H + ':' + i + ':' +s;
        return t;
});
Vue.filter('dateTime',
    function(value) {
        var dateTime = new Date(parseInt(value *1000));
        var Y = dateTime.getFullYear(),
            m = dateTime.getMonth() + 1,
            d = dateTime.getDate(),
            H = dateTime.getHours(),
            i = dateTime.getMinutes(),
            s = dateTime.getSeconds();
            if(m < 10) {
                m = '0' + m;
            }
            if(d < 10) {
                d = '0' + d;
            }
            if(H < 10) {
                H = '0' + H;
            }
            if(i < 10) {
                i = '0' + i;
            }
            if(s < 10) {
                s = '0' + s;
            }
        var t = Y + '-' + m + '-' + d;
        return t;
});
Vue.filter('formatTime',
    function(value) {
        var dateTime = new Date(parseInt(value *1000));
        var Y = dateTime.getFullYear(),
            m = dateTime.getMonth() + 1,
            d = dateTime.getDate(),
            H = dateTime.getHours(),
            i = dateTime.getMinutes(),
            s = dateTime.getSeconds();
            var time = Math.round(new Date().getTime()/1000);
            
            if(time - parseInt(value) < 60) {
                return time - parseInt(value)+'秒前';
            }
            if(time - parseInt(value) > 60 && time - parseInt(value) < 3600) {
                return new Date(parseInt((time - parseInt(value)) *1000)).getMinutes()+'分钟前';
            }
            if(time - parseInt(value) > 3600 && time - parseInt(value) < 86400) {
                return new Date(parseInt((time - parseInt(value)) *1000)).getHours()+'小时前';
            }
            if(time - parseInt(value) > 86400 && time - parseInt(value) < 172800) {
                return '1天前';
            }
            if(m < 10) {
                m = '0' + m;
            }
            if(d < 10) {
                d = '0' + d;
            }
            if(H < 10) {
                H = '0' + H;
            }
            if(i < 10) {
                i = '0' + i;
            }
            if(s < 10) {
                s = '0' + s;
            }
        var t = Y + '-' + m + '-' + d + ' ' + H + ':' + i + ':' +s;
        return t;
});
window.axios.defaults.headers['access-key'] = 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL3';
window.axios.defaults.headers['terminal'] = 'pc';
if (localStorage.getItem('mip_userInfo') != undefined) {
    window.axios.defaults.headers['uid'] = JSON.parse(localStorage.getItem('mip_userInfo')).uid;
    window.axios.defaults.headers['access-token'] = JSON.parse(localStorage.getItem('mip_userInfo')).accessToken[0]['access-token'];
    var rolesNodeList = JSON.parse(localStorage.getItem('mip_userInfo')).rolesNodeList ? JSON.parse(localStorage.getItem('mip_userInfo')).rolesNodeList : [];
    var roleList = JSON.parse(localStorage.getItem('mip_userInfo')).roleList ? JSON.parse(localStorage.getItem('mip_userInfo')).roleList : [];
    var role = {};
    for (i = 0; i <　rolesNodeList.length; i++) {
        role[rolesNodeList[i][name]] = '';
    }
    for (i = 0; i <　roleList.length; i++) {
        role[roleList[i].name] = roleList[i].name;
    }
    Vue.prototype.$role = role;
    Vue.prototype.$userInfo = JSON.parse(localStorage.getItem('mip_userInfo'));
}

var mip = {
    ajax: function(url,param){
        return new Promise(function(resolve, reject){
            axios.post(url,param).then(function (res) {
                if (res.status == 200) {
                    if(res.data.code == undefined) {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.data
                        });
                        return false;
                    }
                    if (res.data.code == 1005) {
                        axios.post('/api/User/loginOut','').then(function (res) {
                            location.href = "/login.html";
                        });
                    }
                    if (res.data.code != 200 && res.data.code != 1 &&  res.data.code != -1 ) {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.data.msg + ' 错误代码:' +res.data.code
                        });
                    } else {
                        resolve(res.data);
                    }
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: '系统错误'
                    });
                }
            }, function(err){
                if (err.config.url == '/install/installPost') {
                    if (err.response.status == 404) {
                        if (err.response.headers.server.indexOf('nginx') > -1) {
                            Vue.prototype.$message({
                                type: 'error',
                                message: '404错误，nginx环境需要配置伪静态规则'
                            });
                        } else {
                            Vue.prototype.$message({
                                type: 'error',
                                message: '404错误，您需要配置环境的伪静态'
                            });
                        }
                    }
                    if (err.response.status == 500) {
                        Vue.prototype.$message({
                            type: 'error',
                            message: '500错误,如果是mysql5.7 请降低版本'
                        });
                        return false;
                    }
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: '系统错误，错误代码：' + err.response.status
                    });
                }
            })
        });
    },
}

Vue.prototype.$mip = mip;