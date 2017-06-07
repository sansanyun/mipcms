    new Vue({
      el: "#app",
      data: function(){
        return {
             form: {
                  dbhost: "127.0.0.1",
                  dbport: "3306",
                  dbuser: "root",
                  dbpw: "",
                  dbname: "",
                  dbprefix: "mip_",
                },
            rules: {
                dbhost: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                dbport: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                dbuser: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                dbpw: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                dbname: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                username: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                password: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
                rpasswrod: [
                    { required: true, message: "必填项", trigger: "blur" },
                ],
            }
        }
      },
      methods: {
        submitForm:function(formName) {
            this.$refs[formName].validate((valid) => {
              if (valid) {
                        var _this = this
                        this.$mip.ajax('/install/installPost', {
                             "dbhost": this.form.dbhost,
                            "dbport": this.form.dbport,
                            "dbuser": this.form.dbuser,
                            "dbpw": this.form.dbpw,
                            "dbname": this.form.dbname,
                            "dbprefix": _this.form.dbprefix,
                        }).then(function (res) {
                            if (res.code == 1) {
                               location.href="/"
                            } else {
                                Vue.prototype.$message({
                                    type: "error",
                                    message: res.msg
                                });
                            }
                        });
              } else {
                return false;
              }
            });
          },
        }
    });