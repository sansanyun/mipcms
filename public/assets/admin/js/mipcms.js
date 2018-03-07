window.axios.defaults.headers['access-key'] = 'cFaLOmUGoz9URROtxaAqe37vHSlI0LL3';
window.axios.defaults.headers['terminal'] = 'pc';
if(localStorage.getItem('mip_userInfo') != undefined) {
    axios.defaults.headers['uid'] = JSON.parse(localStorage.getItem('mip_userInfo')).uid;
    axios.defaults.headers['access-token'] = JSON.parse(localStorage.getItem('mip_userInfo')).accessToken[0]['access-token'];
    localStorage.setItem('access-token',JSON.parse(localStorage.getItem('mip_userInfo')).accessToken[0]['access-token']);
    var rolesNodeList = JSON.parse(localStorage.getItem('mip_userInfo')).rolesNodeList ? JSON.parse(localStorage.getItem('mip_userInfo')).rolesNodeList : [];
    var roleList = JSON.parse(localStorage.getItem('mip_userInfo')).roleList ? JSON.parse(localStorage.getItem('mip_userInfo')).roleList : [];
    var role = {};
    for(var i = 0; i < rolesNodeList.length; i++) {
        role[rolesNodeList[i][name]] = '';
    }
    for(var i = 0; i < roleList.length; i++) {
        role[roleList[i].name] = roleList[i].name;
    }
    Vue.prototype.$role = role;
    Vue.prototype.$userInfo = JSON.parse(localStorage.getItem('mip_userInfo'));
}
var mip = {
    ajax(url, param) {
        return new Promise(function(resolve, reject) {
             
            axios.post(url, param).then(function(res) {
                if(res.status == 200) {
                    if(res.data.code == undefined) {
                        iview.Message.error('请求无效');
                        return false;
                    }
                    if(res.data.code != 200 && res.data.code != 1 && res.data.code != -1) {
                        iview.Message.error(res.data.msg + ' 错误代码:' + res.data.code);
                    } else {
                        if (res.data.code == -1) {
                            iview.Message.error(res.data.msg);
                        }
                    }
                    resolve(res.data);
                } else {
                    iview.Message.error('系统错误');
                }
            }, function(err) {
                iview.Message.error('系统错误，错误代码：' + err);
            })
        });
    },
}

Vue.prototype.$mip = mip;