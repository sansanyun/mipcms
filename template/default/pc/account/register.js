new Vue({
    el: '#app',
    data: function(){
        return {
            ruleForm: {
                username: '',
                password: '',
                rpassword: '',
                captcha: '',
            },
            show: 'login-reg show',
            captchaImg: '/captcha.html',
            registrationAgreementChecked: true,
            dialogRegistrationAgreement: false,
            rules: {
                username: [
                    { required: true, message: '请输入用户名', trigger: 'blur' },
                    { min: 2, max: 25, message: '长度在 2 到25 个字符', trigger: 'blur' }
                ],
                password: [
                    { required: true, message: '请输入密码', trigger: 'blur' },
                    { min: 5, max: 25, message: '长度在 5 到25 个字符', trigger: 'blur' }
                ],
                rpassword: [
                    { required: true, message: '请重复输入密码', trigger: 'blur' },
                    { min: 5, max: 25, message: '长度在 5 到25 个字符', trigger: 'blur' }
                ],
                captcha: [
                    { required: true, message: '请输入验证码', trigger: 'blur' },
                    { min: 4, max: 4, message: '长度为4个字符', trigger: 'blur' }
                ],
            }
        }
    },
    methods: {
        refreshCaptcha: function() {
            this.captchaImg = '/captcha.html?t='+(new Date()).getTime();
        },
        viewRegistrationAgreement: function() {
            this.dialogRegistrationAgreement = true;
        },
        submitForm: function(formName) {
            this.$refs[formName].validate((valid) => {
                if (valid) {
                    if(this.ruleForm.password != this.ruleForm.rpassword) {
                        this.$message({
                            type: 'error',
                            message: '两次输入的密码不一致'
                        });
                        return false;
                    }
                    if(this.registrationAgreementChecked == false) {
                        this.$message({
                            type: 'error',
                            message: '请同意本站用户协议'
                        });
                        return false;
                    }
                    _this = this;
                    this.$mip.ajax('/api/user/userAdd',{
                        terminal: 'pc',
                        username: this.ruleForm.username,
                        password: md5(this.ruleForm.password),
                        captcha: this.ruleForm.captcha,
                    }).then(function (res) {
                        if (res.code == 1) {
                            localStorage.setItem('isShowRegisterSuccessInfo','');
                            window.location.href='/login.html?route=register&why=registerSuccess';
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