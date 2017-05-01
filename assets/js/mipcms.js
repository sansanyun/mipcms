new Vue({
    el: '#mip_header',
    data: function(){
        return {
            avatarImg: '/assets/images/avatar.jpg',
        }
    },
    mounted() {
    },
    methods: {
        loginOut: function() {
            _this = this;
            this.$mip.ajax('/api/User/loginOut', {
            }).then(function (res) {
                if (res.code == 1) {
                    Vue.prototype.$message({
                        type: 'success',
                        message: res.msg
                    });
                    location.href = location.href;
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