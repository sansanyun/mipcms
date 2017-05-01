new Vue({
    el: '#app',
    data:{
        showAction: 'display: black;',
        hiddenAction: 'display: none;',
        tabsValue:'list',
        isShowTab:false,
        searchData:"",
        isDelDisabled:true,
        articleList:'',
        multipleSelection:[],
        loading: false,
        currentPage: 1,
        limit:10,
        total:this.total,
        category: {
            categoryList: [],
            dialogCategory: false,
            dialogCategoryTitle: '',
            id: '',
            name: '',
            url_name: '',
            description: '',
            keywords: '',
            categoryRules: {
                name: [
                    { required: true, message: '请输入名称', trigger: 'blur' }
                ],
            },
            categoryStatus: false,
        },
        publish: {
            title: '',
            cid: '',
            categoryList: [],
            publish_time: '',
            is_recommend: false,
            editor: '',
            content: '', 
            tagsList: [],
            tags: [],
            currentStatus: 'publish',
            toolbar: ['title', 'bold', 'italic', 'underline', 'strikethrough', '|', 'ol', 'ul', 'blockquote', 'code',  '|', 'link', 'image', 'hr']
        }
    },
    mounted: function() {
        this.getPageList();
        this.getCategoryList();
        this.getTags();
    },
    methods:{

        createEditor: function() {
            var _this = this ;
            this.publish.editor = new Simditor({
                textarea: document.getElementById('article_editor'),
                toolbar: _this.publish.toolbar,
                upload: {
                    url: '/api/Upload/imgUpload',
                    params: {
                        type: 'article',
                    },
                    fileKey: 'fileDataFileName', 
                    connectionCount: 3,
                    leaveConfirm: '正在上传文件'
                },
                pasteImage: true,
            });
        },
        getTags: function() {
            this.$mip.ajax('/api/tag/tagsSelect', {
            }).then(function (res) {
                if (res.code == 1) {
                    var tagsList = res.data.tagsList;
                        _this.publish.tagsList = tagsList;
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            });
        },
        getItemTags: function() {
            this.$mip.ajax('/api/tag/itemTagsSelectByItem', {
                'itemType':'article',
                'itemId': this.publish.id,
            }).then(function (res) {
                if (res.code == 1) {
                    var tagsList = res.data.tagsList;
                    console.log(tagsList);
                    if (tagsList.length > 0) {
                        for (var i = 0; i < tagsList.length; i++) {
                            tagsList[i].name = tagsList[i].tags.name;
                            _this.publish.tags.push(tagsList[i].name);
                        }
                    }
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            });
        },
        publishPost: function() {
            this.publish.content = this.publish.editor.getValue();
            var timestamp = Date.parse(this.publish.publish_time);
            timestamp = timestamp / 1000;
            if (this.publish.currentStatus == 'edit') {
                this.$mip.ajax('/api/article/articleEdit', {
                   id: this.publish.id,
                   title: this.publish.title,
                   content: this.publish.content,
                   cid: this.publish.cid,
                   is_recommend: this.publish.is_recommend ? '1' : '0',
                   tags: this.publish.tags.join(','),
                   publish_time: timestamp?timestamp:'',
                }).then(function (res) {
                    if (res.code == 1) {
                        Vue.prototype.$message({
                            type: 'success',
                            message: res.msg
                        });
                        _this.getPageList();
                        _this.tabsValue = "list";
                        _this.isShowTab = false;
                    } else {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                });
            } else {
                this.$mip.ajax('/api/article/articleAdd', {
                   title: this.publish.title,
                   content: this.publish.content,
                   cid: this.publish.cid,
                   is_recommend: this.publish.is_recommend ? '1' : '0',
                   tags: this.publish.tags.join(','),
                   publish_time: timestamp?timestamp:'',
                }).then(function (res) {
                    if (res.code == 1) {
                        Vue.prototype.$message({
                            type: 'success',
                            message: res.msg
                        });
                        _this.getPageList();
                        _this.tabsValue = "list";
                        _this.isShowTab = false;
                    } else {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                });
            }
           
        },

        addCategoryDialog: function() {
            this.category.categoryStatus = false;
            this.category.name = '';
            this.category.url_name = '';
            this.category.description = '';
            this.category.keywords = '';
            this.category.id = '';
            this.category.dialogCategory = true;
            this.category.dialogCategoryTitle = '添加分类';
        },
        categoryPost(val,param) {
             this.$refs[val].validate((valid) => {
                if (valid) {
                    _this = this;
                    this.category.dialogCategory = false;
                    if (this.category.categoryStatus == false) {
                        this.$mip.ajax('/api/article/categoryAdd', {
                           name: this.category.name,
                           url_name: this.category.url_name,
                           description: this.category.description,
                           keywords: this.category.keywords,
                        }).then(function (res) {
                            if (res.code == 1) {
                                Vue.prototype.$message({
                                    type: 'success',
                                    message: res.msg
                                });
                                _this.getCategoryList();
                            } else {
                                Vue.prototype.$message({
                                    type: 'error',
                                    message: res.msg
                                });
                            }
                        });
                    } else {
                        this.$mip.ajax('/api/article/categoryEdit', {
                           id: param.id,
                           name: this.category.name,
                           url_name: this.category.url_name,
                           description: this.category.description,
                           keywords: this.category.keywords,
                        }).then(function (res) { 
                            if (res.code == 1) {
                                Vue.prototype.$message({
                                    type: 'success',
                                    message: res.msg
                                });
                                _this.getCategoryList();
                            } else {
                                Vue.prototype.$message({
                                    type: 'error',
                                    message: res.msg
                                });
                            }
                        });
                    }
                }
            });
        },
        categoryDel(index,val) {
            this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                _this = this;
                this.$mip.ajax('/api/article/categoryDel', {
                    id: val.id,
                }).then(function (res) { 
                    if (res.code == 1) {
                        Vue.prototype.$message({
                            type: 'success',
                            message: res.msg
                        });
                        _this.getCategoryList();
                    } else {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                });
            }).catch(() => {
                
            });
        },
        getCategoryList: function() {
            _this = this;
            this.$mip.ajax('/api/article/categorySelect', {
                
            }).then(function (res) {
                if (res.code == 1) {
                    _this.category.categoryList = res.data.categoryList;
                    _this.publish.categoryList =  _this.category.categoryList;
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            });
        },
        categoryEditDialog(index,row) {
            this.category.dialogCategory = true;
            this.category.categoryStatus = true;
            this.category.name = row.name;
            this.category.url_name = row.url_name;
            this.category.description = row.description;
            this.category.keywords =  row.keywords;
            this.category.id = row.id;
            this.category.dialogCategoryTitle = '编辑分类';
        },

        itemAddTap:function() {
            this.tabsValue = "newAdd";
            this.isShowTab = true;
            this.publish.currentStatus = 'publish';
            this.publish.id = '';
            this.publish.title = '';
            this.publish.cid = '';
            this.publish.tags = [];
            this.publish.is_recommend = false;
            this.publish.publish_time =new Date();
            this.getCategoryList();
            var _this = this;
            setTimeout(function() {
                _this.createEditor();
            }, 100);
        },
        handleListSelectionChange:function(val) {
          this.multipleSelection = val;
          if(val.length == 0) {
            this.isDelDisabled = true;
          } else {
            this.isDelDisabled = false;
          }
        },
        itemsDel:function() {
          this.$message({
            message: '不着急 尚未开通'
          });
        },
        articleDel: function(index, row) {
            this.$confirm('此操作将永久删除该内容, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.$mip.ajax('/api/article/articleDel', {
                    id:row.id,
                }).then(function (res) {
                    if (res.code == 1) {
                        Vue.prototype.$message({
                          type: 'success',
                          message: res.msg
                        });
                    } else {
                        Vue.prototype.$message({
                            type: 'error',
                            message: res.msg
                        });
                    }
                    _this.getPageList();
                });
            }).catch(() => {
                return false;   
            });
        },
        getPageList:function() {
            this.loading = true;
            _this = this;
            this.$mip.ajax('/api/article/articlesSelect', {
                  page:this.currentPage,
                  status:'all',
                  limit:this.limit,
            }).then(function (res) {
                if (res.code == 1) {
                    _this.articleList = res.data.articleList;
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
        articleEditTap:function(index, row) {
            this.tabsValue = "newAdd";
            this.isShowTab = true;
            this.publish.currentStatus = 'edit';
            this.publish.id = row.id;
            this.publish.title = row.title;
            this.publish.cid = row.cid;
            this.publish.is_recommend = row.is_recommend == 0 ? false : true;
            this.publish.tags = [];
            this.publish.publish_time = row.publish_time?new Date(parseInt(row.publish_time) * 1000):'';
            this.getItemTags();
            this.getCategoryList();
            var _this = this;
            setTimeout(function() {
                _this.createEditor();
                _this.publish.editor.setValue(row.content);
            }, 100);
        },
        
        removeTab:function(targetName) {
            var publishContent = this.publish.editor.getValue();
            if (publishContent) {
                this.$confirm('此操作将会清空编辑器中内容，是否确认？', '提示', {
                    confirmButtonText: '确定',
                    cancelButtonText: '取消',
                    type: 'warning'
                }).then(() => {
                    this.tabsValue = 'list';
                    this.isShowTab = false;
                }).catch(() => {
                    return false;   
                });
            } else {
                this.tabsValue = 'list';
                this.isShowTab = false;
            }
        },
        handleSizeChange(val) {
          this.limit = val;
          this.getPageList();
        },
        handleCurrentChange(val) {
          this.currentPage = val;
          this.getPageList();
        },
        handleSearchClick:function() {
          this.getPageList();
          this.$message({
            message: '尚未开通'
          });
        },
    },

})