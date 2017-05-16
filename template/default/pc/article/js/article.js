new Vue({
    el: '#mip_main',
    data: function(){
        return {
            articleComments: '',
            commentEditData: '',
            commentId: '',
            articeId: {$itemInfo['id']},
            commentsList: [],
            dialogComment: false,
            currentPage: 1,
            limit:5,
            total: '',
        }
    },
    mounted() {
        this.getCommentsListData();
    },
    methods: {
        commentsCurrentChange: function(val) {
          this.currentPage = val;
          this.getCommentsListData();
        },
        commentsAdd: function() {
            _this = this;
            this.$mip.ajax('/api/article/commentsAdd', {
                articleId: this.articeId,
                content: this.articleComments,
            }).then(function (res) { 
                if (res.code == 1) {
                    Vue.prototype.$message({
                        type: 'success',
                        message: res.msg
                    });
                    _this.articleComments = '',
                    _this.getCommentsListData();
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            });
        },
        commentDel: function(val) {
            this.$confirm('此操作将永久删除, 是否继续?', '提示', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
            }).then(() => {
                this.commentId = val.id;
                _this = this;
                this.$mip.ajax('/api/article/commentDel', {
                    id: this.commentId,
                }).then(function (res) { 
                    if (res.code == 1) {
                        Vue.prototype.$message({
                            type: 'success',
                            message: res.msg
                        });
                        _this.getCommentsListData();
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
        getCommentsListData: function() {
            _this = this;
            this.$mip.ajax('/api/article/commentsSelect', {
                itemId: this.articeId,
                page:this.currentPage,
                limit:this.limit,
            }).then(function (res) { 
                if (res.code == 1) {
                      commentsList = res.data.itemList;
                      for (var i = 0; i < commentsList.length; i++) {
                          commentsList[i].commentsEditRole = _this.$role.commentsEdit && commentsList[i]['uid'] == _this.$userInfo['uid'];
                          commentsList[i].commentsDelRole = _this.$role.commentDel && commentsList[i]['uid'] == _this.$userInfo['uid'];
                      }
                      _this.commentsList = commentsList;
                      _this.total = res.data.total;
                } else {
                    Vue.prototype.$message({
                        type: 'error',
                        message: res.msg
                    });
                }
            });
        },
        commentEditDialog:function(val) {
            this.dialogComment = true;
            this.commentEditData = val.content;
            this.commentId = val.id;
        },
        commentEdit: function() {
             _this = this;
            this.$mip.ajax('/api/article/commentsEdit', {
                articleId: this.articeId,
                id: this.commentId,
                content: this.commentEditData,
            }).then(function (res) {
                if (res.code == 1) {
                    _this.dialogComment = false;
                    Vue.prototype.$message({
                        type: 'success',
                        message: res.msg
                    });
                    _this.articleComments = '',
                    _this.getCommentsListData();
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