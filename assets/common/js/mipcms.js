new Vue({
    el: '#mip_header',
    data: function(){
        return {
            avatarImg: '/' + mipGlobal.assets + '/' + mipGlobal.tplName + '/images/avatar.jpg',
            globalSearchKey: '',
        }
    },
    mounted:function mounted() {
    },
    methods: {
        globalSearchClick: function(){
            location.href = '/search/'+this.globalSearchKey;
        },
        loginOut: function() {
            _this = this;
            this.$mip.ajax('/api/User/loginOut', {
            }).then(function (res) {
                if (res.code == 1) {
                    Vue.prototype.$message({
                        type: 'success',
                        message: res.msg
                    });
                    location.href = '/';
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            });
        },
    },
});