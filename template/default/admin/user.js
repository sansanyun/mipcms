new Vue({
        el: '#app',
        data:{
            tabsValue:'list',
            isShowTab:false,
            searchData:"",
            isDelDisabled:true,
            usersList:'',
            multipleSelection:[],
            loading: false,
            currentPage: 1,
            currentStatus: 'publish',
            currentSisabled: false,
            limit:10,
            total:this.total,
            ruleForm: {
                username: '',
                password: '',
                rpassword: '',
                sex: '0',
                groupList: [],
                group_id: '',
                email: '',
                mobile: '',
                qq: '',
                signature: '',
                uid: '',
            },
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
                  { min: 5, max: 25, message: '长度在 5 到25 个字符', trigger: 'blur' }
                ],
            }
        },
        mounted: function() {
            this.getPageList();
            this.getGroupList();
            _this = this;
        },
        methods:{
            removeTab:function(targetName) {
                this.tabsValue = 'list';
                this.isShowTab = false;
                this.$refs['ruleForm'].resetFields();
            },
            handleSizeChange:function(val) {
                this.limit = val;
                this.getPageList();
            },
            handleCurrentChange:function(val) {
                this.currentPage = val;
                this.getPageList();
            },
            handleSearchClick:function() {
                this.getPageList();
                this.$message({
                    message: '尚未开通'
                });
            },
            addItem:function() {
                this.currentStatus = 'publish';
                this.currentSisabled = true;
                this.tabsValue = "newAdd";
                this.ruleForm.uid = '';
                this.ruleForm.group_id = 2;
                this.ruleForm.username = '';
                this.ruleForm.password = '';
                this.ruleForm.sex = '1';
                this.ruleForm.email = '';
                this.ruleForm.mobile = '';
                this.ruleForm.qq = '';
                this.ruleForm.signature = '';
                this.isShowTab = true;
            },
            
            delItems:function() {
                var uids = [];
                for (var i = 0; i < this.multipleSelection.length; i++) {
                    uids.push(this.multipleSelection[i].uid);
                }
                uids = uids.join(',');
                this.$mip.ajax('/api/user/delUsers',{
                    uids:uids,
                }).then(function (res) {
                    if(res.status==200){
                        if (res.code == 1) {
                            Vue.prototype.$message({
                              type: 'success',
                              message: res.msg
                            });
                            this.getPageList();
                        } else {
                            Vue.prototype.$message({
                                type: 'error',
                                message: res.msg
                            });
                        }
                    }
                })
            },
            handleListSelectionChange:function(val) {
                this.multipleSelection = val;
                if(val.length == 0) {
                    this.isDelDisabled = true;
                } else {
                    this.isDelDisabled = false;
                }
            },
            handleEdit:function(index, row) {
                this.tabsValue = "newAdd";
                this.isShowTab = true;
                this.currentStatus = 'edit';
                this.currentSisabled = false;
                this.ruleForm.group_id = row.group_id;
                this.ruleForm.uid = row.uid;
                this.ruleForm.username = row.username;
                this.ruleForm.password = '';
                this.ruleForm.sex = row.sex + "";
                this.ruleForm.email = row.email;
                this.ruleForm.mobile = row.mobile;
                this.ruleForm.qq = row.qq;
                this.ruleForm.signature = row.signature;
            },
            handleDelete:function(index, row) {
                this.$confirm('此操作将永久删除该内容, 是否继续?', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.$mip.ajax('/api/user/userDel',{
                        uid:row.uid,
                    }).then(function (res) {
                        if (res.code == 1) {
                            Vue.prototype.$message({
                              type: 'success',
                              message: res.msg
                            });
                            _this.getPageList();
                        } else {
                            Vue.prototype.$message({
                                type: 'error',
                                message: res.msg
                            });
                        }
                    })
               
                }).catch(() => {
                    return false;   
                });
            },
            getPageList:function() {
                this.loading = true;
                this.$mip.ajax('/api/user/usersSelect',{
                    page:this.currentPage,
                    status:'all',
                    limit:this.limit,
                }).then(function (res) {
                    if (res.code == 1) {
                        _this.usersList = res.data.usersList;
                        _this.total = res.data.total;
                    } else {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                    _this.loading = false;
                });
            },
            getGroupList:function() {
                this.loading = true;
                this.$mip.ajax('/api/User_group/userGroupSelect',{
                }).then(function (res) {
                    if (res.code == 1) {
                        var groupList = res.data;
                        for (var i = 0; i < groupList.length; i++) {
                        	groupList[i].value = groupList[i].group_id;
                            groupList[i].label = groupList[i].name;
                        }
                        _this.ruleForm.groupList = groupList;
                        console.log(_this.ruleForm);
                    } else {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                    _this.loading = false;
                });
            },
            submitForm(formName) {
                if (this.currentStatus == 'edit') {
                    _this = this;
                    this.$mip.ajax('/api/user/userEdit',{
                        uid:this.ruleForm.uid,
                        username:this.ruleForm.username,
                        password:this.ruleForm.password ? md5(this.ruleForm.password) : '',
                        sex:parseInt(this.ruleForm.sex),
                        group_id: parseInt(this.ruleForm.group_id),
                        email:this.ruleForm.email,
                        mobile:this.ruleForm.mobile,
                        qq:this.ruleForm.qq,
                        signature:this.ruleForm.signature,
                        status:0,
                    }).then(function (res) {
                        if (res.code == 1) {
                            _this.tabsValue = 'list';
                            _this.isShowTab = false;
                            _this.$refs[formName].resetFields();
                            _this.getPageList();
                        } else {
                            Vue.prototype.$message({
                                type: 'error',
                                message: res.msg
                            });
                        }
                    });
                    
                } else {
                    this.$refs[formName].validate((valid) => {
                        if (valid) {
                            if (!this.ruleForm.password) {
                                Vue.prototype.$message({
                                    type: 'warning',
                                    message: '请输入密码'
                                });
                                return false;
                            }
                            _this = this;
                            this.$mip.ajax('/api/user/userCreate',{
                                terminal:'pc',
                                username:this.ruleForm.username,
                                password:md5(this.ruleForm.password),
                                sex: parseInt(this.ruleForm.sex),
                                group_id: parseInt(this.ruleForm.group_id),
                                email:this.ruleForm.email,
                                mobile:this.ruleForm.mobile,
                                qq:this.ruleForm.qq,
                                signature:this.ruleForm.signature,
                            }).then(function (res) {
                                if (res.code == 1) {
                                    _this.tabsValue = 'list';
                                    _this.isShowTab = false;
                                    _this.$refs[formName].resetFields();
                                    _this.getPageList();
                                } else {
                                    Vue.prototype.$message({
                                        type: 'error',
                                        message: res.msg
                                    });
                                }
                            });
                        } else {
                          return false;
                        }
                    });
                }
            },
        }
    });
