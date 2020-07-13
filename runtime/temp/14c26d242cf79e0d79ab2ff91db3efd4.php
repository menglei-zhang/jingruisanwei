<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:53:"/webdata/snake/application/admin/view/user/index.html";i:1557994889;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>管理员列表</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="__CSS__/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="__CSS__/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__CSS__/plugins/bootstrap-table/bootstrap-table.min.css" rel="stylesheet">
    <link href="__CSS__/animate.min.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="__CSS__/plugins/sweetalert/sweetalert.css" rel="stylesheet">
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <!-- Panel Other -->
    <div class="ibox float-e-margins">
        <div class="ibox-title">
            <h5>管理员列表</h5>
             <hr>
            <h5 style="color:red">*注释：1.添加管理员时请选择其对应的权限</h5>
            <h5 style="color:red">2.添加好后请为其分组</h5>
<!--             <h5 style="color:red">3.添加顶级节点是添加新组的意思，权限所对应的组长即可</h5> -->
        </div>
        <div class="ibox-content">
            <div class="form-group clearfix col-sm-1">
                <?php if(authCheck('user/useradd')): ?>
                <a href="<?php echo url('user/userAdd'); ?>">
                    <button class="btn btn-outline btn-primary" type="button">添加管理员</button>
                </a>
                <?php endif; ?>
            </div>
            <!--搜索框开始-->
            <form id='commentForm' role="form" method="post" class="form-inline pull-right">
                <div class="content clearfix m-b">
                    <div class="form-group">
                        <label>管理员名称：</label>
                        <input type="text" class="form-control" id="username" name="user_name">
                    </div>
                    <div class="form-group">
                        <button class="btn btn-primary" type="button" style="margin-top:5px" id="search"><strong>搜 索</strong>
                        </button>
                    </div>
                </div>
            </form>
            <!--搜索框结束-->
            <div class="example-wrap">
                <div class="example">
                    <table id="cusTable">
                        <thead>
                        <th data-field="user_id">管理员ID</th>
                        <th data-field="user_name">管理员名称</th>
                        <th data-field="role_name">管理员角色</th>
                        <th data-field="login_times">登录次数</th>
                        <th data-field="last_login_ip">上次登录ip</th>
                        <th data-field="last_login_time">上次登录时间</th>
                        <th data-field="real_name">真是姓名</th>
                        <th data-field="status">状态</th>
                        <th data-field="operate">操作</th>
                        </thead>
                    </table>
                </div>
            </div>
            <!-- End Example Pagination -->
        </div>
    </div>
</div>
<div class="zTreeDemoBackground left" style="display: none" id="role">
    <input type="hidden" id="nodeid">
    <div class="form-group">
        <div class="col-sm-5 col-sm-offset-2">
            <ul id="treeType" class="ztree"></ul>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4 col-sm-offset-4" style="margin-bottom: 15px">
            <input type="button" value="确认分配" class="btn btn-primary" id="postform"/>
        </div>
    </div>
</div>
<!-- End Panel Other -->
</div>
<script src="__JS__/jquery.min.js?v=2.1.4"></script>
<script src="__JS__/bootstrap.min.js?v=3.3.6"></script>
<script src="__JS__/content.min.js?v=1.0.0"></script>
<script src="__JS__/plugins/bootstrap-table/bootstrap-table.min.js"></script>
<script src="__JS__/plugins/bootstrap-table/bootstrap-table-mobile.min.js"></script>
<script src="__JS__/plugins/bootstrap-table/locale/bootstrap-table-zh-CN.min.js"></script>
<script src="__JS__/plugins/suggest/bootstrap-suggest.min.js"></script>
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/sweetalert/sweetalert.min.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>
<link rel="stylesheet" href="__JS__/plugins/zTree/zTreeStyle.css" type="text/css">
<script type="text/javascript" src="__JS__/plugins/zTree/jquery.ztree.core-3.5.js"></script>
<script type="text/javascript" src="__JS__/plugins/zTree/jquery.ztree.excheck-3.5.js"></script>
<!-- <script type="text/javascript" src="__JS__/plugins/zTree/jquery.ztree.exedit-3.5.js"></script> -->
<script type="text/javascript">
    function initTable() {
        //先销毁表格
        $('#cusTable').bootstrapTable('destroy');
        //初始化表格,动态从服务器加载数据
        $("#cusTable").bootstrapTable({
            method: "get",  //使用get请求到服务器获取数据
            url: "./index", //获取数据的地址
            striped: true,  //表格显示条纹
            pagination: true, //启动分页
            pageSize: 10,  //每页显示的记录数
            pageNumber:1, //当前第几页
            pageList: [5, 10, 15, 20, 25],  //记录数可选列表
            sidePagination: "server", //表示服务端请求
            paginationFirstText: "首页",
            paginationPreText: "上一页",
            paginationNextText: "下一页",
            paginationLastText: "尾页",
            queryParamsType : "undefined",
            queryParams: function queryParams(params) {   //设置查询参数
                var param = {
                    pageNumber: params.pageNumber,
                    pageSize: params.pageSize,
                    searchText:$('#username').val()
                };
                return param;
            },
            onLoadSuccess: function(res){  //加载成功时执行
                if(111 == res.code){
                    window.location.reload();
                }
                layer.msg("加载成功", {time : 1000});
            },
            onLoadError: function(){  //加载失败时执行
                layer.msg("加载数据失败");
            }
        });
    }

    $(document).ready(function () {
        //调用函数，初始化表格
        initTable();

        //当点击查询按钮的时候执行
        $("#search").bind("click", initTable);
    });

        //分配权限
    function giveQx(id){
        $("#nodeid").val(id);
        //加载层
        index2 = layer.load(0, {shade: false}); //0代表加载的风格，支持0-2

        // 获取权限信息
        $.getJSON("<?php echo url('user/giveAccess'); ?>", {'type' : 'get', 'id' : id}, function(res){
            layer.close(index2);
            if(1 == res.code){
                zNodes = JSON.parse(res.data);  //将字符串转换成obj

                // console.log(res);

                //页面层
                index = layer.open({
                    type: 1,
                    area:['350px', '400px'],
                    title:'小组分配',
                    skin: 'layui-layer-demo', //加上边框
                    content: $('#role')
                });

                //设置zetree
                // var setting = {
                //     check:{
                //          enable: true, chkStyle: "radio", radioType: "level" 
                //     },
                //     data: {
                //         simpleData: {
                //             enable: true
                //         }
                //     }
                // };



        var setting = {
            check: {
                enable: true,
                chkStyle: "radio",
                radioType: "level"
            },
            data: {
                simpleData: {
                    enable: true
                }
            }
        };


            var type = $("#level").attr("checked")? "level":"all";
            setting.check.radioType = type;

                $.fn.zTree.init($("#treeType"), setting, zNodes);
                var zTree = $.fn.zTree.getZTreeObj("treeType");

                // console.log(zTree);

                zTree.expandAll(true);

            }else if(111 == res.code){
                window.location.reload();
            }else{
                layer.alert(res.msg, {title: '友情提示', icon: 2});
            }

        });
    }
    //确认分配权限
    $("#postform").click(function(){
        var zTree = $.fn.zTree.getZTreeObj("treeType");
        var nodes = zTree.getCheckedNodes(true);
        var NodeString = '';
        $.each(nodes, function (n, value) {
            // if(n>0){
            //     NodeString += ',';
            // }
            NodeString = value.id;
        });
        var id = $("#nodeid").val();
        //写入库
        $.post("<?php echo url('user/giveAccess'); ?>", {'type' : 'give', 'id' : id, 'rule' : NodeString}, function(res){
            layer.close(index);
            if(1 == res.code){
                layer.alert(res.msg, {title: '友情提示', icon: 1, closeBtn: 0}, function(){
                    initTable();
                });
            }else if(111 == res.code){
                window.location.reload();
            }else{
                layer.alert(res.msg, {title: '友情提示', icon: 2});
            }

        }, 'json')
    })

    function userDel(id){
        layer.confirm('确认删除此管理员?', {icon: 3, title:'提示'}, function(index){
            //do something
            $.getJSON("<?php echo url('user/userDel'); ?>", {'id' : id}, function(res){
                if(1 == res.code){
                    layer.alert(res.msg, {title: '友情提示', icon: 1, closeBtn: 0}, function(){
                        initTable();
                    });
                }else if(111 == res.code){
                    window.location.reload();
                }else{
                    layer.alert(res.msg, {title: '友情提示', icon: 2});
                }
            });

            layer.close(index);
        })

    }
</script>
</body>
</html>
