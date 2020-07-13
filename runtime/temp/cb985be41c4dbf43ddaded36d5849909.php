<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:82:"/www/wwwroot/jingrui.gugangkf.cn/application/admin/view/customer/customeredit.html";i:1556606858;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>添加文章</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="__CSS__/bootstrap.min.css?v=3.3.6" rel="stylesheet">
    <link href="__CSS__/font-awesome.min.css?v=4.4.0" rel="stylesheet">
    <link href="__CSS__/animate.min.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <link href="__JS__/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet">
    <link href="__JS__/layui/css/layui.css"rel="stylesheet">
    <style type="text/css">
        .demo {
            margin-bottom: 15px;
            margin-right: -15px;
            margin-left: -15px;
        }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>修改客户</h5>
                </div>
                <div class="ibox-content">
                    <form class="form-horizontal m-t" id="commentForm" method="post" action="<?php echo url('customer/customeredit'); ?>">
                         <input type="hidden" name="cust_id" value="<?php echo $customer['cust_id']; ?>"/>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">*客户名称全称：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_name" type="text" class="form-control" name="cust_name" required="" aria-required="true"   value="<?php echo $customer['cust_name']; ?>">
                            </div>
                        </div>


                        <div class="form-group">
                            <label class="col-sm-3 control-label">*注册地址：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_place" type="text" class="form-control" name="cust_place" required="" aria-required="true"  value="<?php echo $customer['cust_place']; ?>" >
                            </div>
                        </div>
                         <div class="form-group">
                            <label class="col-sm-3 control-label">*联系人：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_liaison" type="text" class="form-control" name="cust_liaison" required="" aria-required="true" value="<?php echo $customer['cust_liaison']; ?>">
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3 control-label">*联系方式：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_photo" type="text" class="form-control" name="cust_photo" required="" aria-required="true" value="<?php echo $customer['cust_photo']; ?>"></input>
                            </div>
                        </div>
                        
                        <div class="demo">
                            <label class="col-sm-3 control-label">开户银行：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_bank" type="text" class="form-control" name="cust_bank" value="<?php echo $customer['cust_bank']; ?>" ></input>
                            </div>

                        </div>
                          <div class="demo">
                            <label class="col-sm-3 control-label">开户地址：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_kh_place" type="text" class="form-control" name="cust_kh_place" value="<?php echo $customer['cust_kh_place']; ?>" ></input>
                            </div>

                        </div>
                        <div class="demo">
                            <label class="col-sm-3 control-label">开户帐号：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_kh_username" type="text" class="form-control" name="cust_kh_username"  value="<?php echo $customer['cust_kh_username']; ?>"></input>
                            </div>

                        </div>
                        <div class="demo">
                            <label class="col-sm-3 control-label">开户电话号码：</label>
                            <div class="input-group col-sm-7">
                                <input id="cust_kh_photo" type="text" class="form-control" name="cust_kh_photo"  value="<?php echo $customer['cust_kh_photo']; ?>"></input>
                            </div>
                        </div>
                        <div class="demo">
                            <label class="col-sm-3 control-label">财务电话：</label>
                            <div class="input-group col-sm-7">
                                <input id="finance_photo" type="text" class="form-control" name="finance_photo" value="<?php echo $customer['finance_photo']; ?>" ></input>
                            </div>
                        </div>
                        <div class="demo">
                            <label class="col-sm-3 control-label">财务姓名：</label>
                            <div class="input-group col-sm-7">
                                <input id="finance_name" type="text" class="form-control" name="finance_name" value="<?php echo $customer['finance_name']; ?>" ></input>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-4 col-sm-offset-4">
                                <!--<input type="button" value="提交" class="btn btn-primary" id="postform"/>-->
                                <button class="btn btn-primary" type="submit">确认提交</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
<script src="__JS__/jquery.min.js?v=2.1.4"></script>
<script src="__JS__/bootstrap.min.js?v=3.3.6"></script>
<script src="__JS__/content.min.js?v=1.0.0"></script>
<script src="__JS__/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="__JS__/plugins/validate/jquery.validate.min.js"></script>
<script src="__JS__/plugins/validate/messages_zh.min.js"></script>
<script src="__JS__/layui/layui.js"></script>
<script src="__JS__/jquery.form.js"></script>
<script src="__JS__/plugins/ueditor/ueditor.config.js"></script>
<script src="__JS__/plugins/ueditor/ueditor.all.js"></script>
<script type="text/javascript">
var index = '';
    function showStart(){
        index = layer.load(0, {shade: false});
        return true;
    }

    function showSuccess(res){

        layer.ready(function(){
            layer.close(index);
            if(1 == res.code){
                layer.alert(res.msg, {title: '友情提示', icon: 1, closeBtn: 0}, function(){
                    window.location.href = res.data;
                });
            }else if(111 == res.code){
                window.location.reload();
            }else{
                layer.msg(res.msg, {anim: 6});
            }
        });
    }

    $(document).ready(function(){
        // 添加角色
        var options = {
            beforeSubmit:showStart,
            success:showSuccess
        };

        $('#commentForm').submit(function(){
            $(this).ajaxSubmit(options);
            return false;
        });

        $('#keywords').tagsinput('add', 'some tag');
        $(".bootstrap-tagsinput").addClass('col-sm-12').find('input').addClass('form-control')
            .attr('placeholder', '输入后按enter');

        // 上传图片
        layui.use('upload', function(){
            var upload = layui.upload;

            //执行实例
            var uploadInst = upload.render({
                elem: '#test1' //绑定元素
                ,url: "<?php echo url('articles/uploadImg'); ?>" //上传接口
                ,done: function(res){
                    //上传完毕回调
                    $("#thumbnail").val(res.data.src);
                    $("#sm").html('<img src="' + res.data.src + '" style="width:40px;height: 40px;"/>');
                }
                ,error: function(){
                    //请求异常回调
                }
            });
        });

        // var editor = UE.getEditor('container');
    });

    // 表单验证
    $.validator.setDefaults({
        highlight: function(e) {
            $(e).closest(".form-group").removeClass("has-success").addClass("has-error")
        },
        success: function(e) {
            e.closest(".form-group").removeClass("has-error").addClass("has-success")
        },
        errorElement: "span",
        errorPlacement: function(e, r) {
            e.appendTo(r.is(":radio") || r.is(":checkbox") ? r.parent().parent().parent() : r.parent())
        },
        errorClass: "help-block m-b-none",
        validClass: "help-block m-b-none"
    });
</script>
</body>
</html>
