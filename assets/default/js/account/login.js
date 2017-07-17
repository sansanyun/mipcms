   new Vue({
    el: '#app',
    data: function(){
        return {
            ruleForm: {
                username: '',
                password: '',
                captcha: '',
            },
            show: 'login-reg show',
            captchaImg: mipGlobal.rewrite+'/captcha.html',
            rules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 2, max: 25, message: '长度在 2 到25 个字符', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '请输入密码', trigger: 'blur' },
                    { min: 5, max: 25, message: '长度在 5 到25 个字符', trigger: 'blur' }
                ],
                captcha: [
                    { required: true, message: '请输入验证码', trigger: 'blur' },
                    { min: 4, max: 4, message: '长度为4个字符', trigger: 'blur' }
                ],
            }
        }
    },
    mounted: function mounted() {
      this.registerSuccess();
    },
    methods: {
        registerSuccess: function() {
          var why =this.$utils.getUrlParam('why');
          if (why == 'registerSuccess') {
              if(!localStorage.getItem('isShowRegisterSuccessInfo')) {
                  this.$notify({
                  title: '消息提醒',
                  message: '恭喜你，注册成功',
                  type: 'success'
                });
                localStorage.setItem('isShowRegisterSuccessInfo',true);
              }
          }
        },
        refreshCaptcha: function() {
            if (mipGlobal.rewrite) {
                this.captchaImg = mipGlobal.rewrite+'/captcha.html&t='+(new Date()).getTime();
            } else {
                this.captchaImg = '/captcha.html?t='+(new Date()).getTime();
            }
        },
        submitForm: function(formName) {
            var _this = this;
            this.$refs[formName].validate(function(valid) {
                if (valid) {
                    _this.$mip.ajax('/ApiUser/Api_User_User/login', {
                        terminal: 'pc',
                        username: _this.ruleForm.username,
                        password: md5(_this.ruleForm.password),
                        captcha: _this.ruleForm.captcha,
                    }).then(function (res) {
                        _this.refreshCaptcha();
                        if (res.code == 1) {
                            location.href = mipGlobal.returnUrl;
                            localStorage.setItem('mip_userInfo',JSON.stringify(res.data));
                        } else {
                            Vue.prototype.$message({
                                type: 'error',
                                message: res.msg
                            });
                        }
                    });
                }
            });
        },
    }
});