<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:62:"D:\wamp64\www\snake/application/admin\view\order\orderadd.html";i:1557380105;}*/ ?>
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
    <link href="__CSS__/plugins/iCheck/custom.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <!-- <link href="__CSS__/plugins/bootstrap-tagsinput/bootstrap-tagsinput.css" rel="stylesheet"> -->
    <link href="__CSS__/layui/css/layui.css"rel="stylesheet">
        <link href="__CSS__/styles.css" rel="stylesheet">
  
    <link href="__CSS__/comment.css" rel="stylesheet">
    <style>

    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加订单</h5>
                </div>
                <div class="ibox-content">
                    <form class="layui-form form-horizontal m-t " id="commentForm" method="post" action="<?php echo url('order/orderadd'); ?>" enctype="multipart/form-data">

                    <div class="layui-tab">
                      <ul class="layui-tab-title">
                        <li class="layui-this">订单填写</li>
                        <li>文件图片上传</li>
                      </ul>
                        <div class="layui-tab-content">
                           <div class="layui-tab-item layui-show">

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*订单编号：</label>
                                <div class="input-group col-sm-4">
                                    <input id="order_sn" type="text" class="form-control" name="order_sn" required="" aria-required="true" value="123321123456">
                                </div>
                            </div>

<!--                             <div class="form-group">
                                <label class="col-sm-3 control-label">*客户名称：</label>
                                <div class="input-group col-sm-4">
                                    <input id="username" type="text" class="form-control" name="username" required="" aria-required="true">
                                </div>
                            </div>
                             -->
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*客户名称：</label>

                                    <div class="input-group col-sm-4">
                                      <select name="username" lay-verify="required" id="username">
                                        <option value=""></option>
                                      <?php if(is_array($name) || $name instanceof \think\Collection || $name instanceof \think\Paginator): $k = 0; $__LIST__ = $name;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$name): $mod = ($k % 2 );++$k;?>
                                        
                                        <option value="<?php echo $name['cust_name']; ?>" ><?php echo $name['cust_name']; ?></option>
                            
                                      <?php endforeach; endif; else: echo "" ;endif; ?>
                                      </select>
                                    </div>
                            </div>
                                                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*订单金额：</label>
                                <div class="input-group col-sm-4">
                                    <input id="place" type="text" class="form-control" name="place" required="" aria-required="true">
                                </div>
                            </div>
                            <!-- 测试版本 -->
                              <div class="form-group">
                                <label class="col-sm-3 control-label">*业务员名称：</label>
                                <div class="input-group col-sm-4">
        
                                    <select name="workname" lay-verify="required" id="workname">
                                        <option value=""></option>
                                   <?php if(is_array($real) || $real instanceof \think\Collection || $real instanceof \think\Paginator): $k = 0; $__LIST__ = $real;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$real): $mod = ($k % 2 );++$k;?>
                                       
                                        <option value="<?php echo $real['real_name']; ?>,<?php echo $real['user_id']; ?>" ><?php echo $real['real_name']; ?></option>
                            
                                   <?php endforeach; endif; else: echo "" ;endif; ?> 
                                    </select>
                                </div>
                            </div>

                           
                            <div class="form-group">
                              <label class="col-sm-3 control-label">*交货日期：</label>
                              <div class="input-group col-sm-2">
                                <input type="text" name="update" id="update" autocomplete="off" class="layui-input">
                              </div>
                            </div>
                            

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*交货时间：</label>
                                <div class="input-group col-sm-7">
                                    <div class="radio  col-sm-6">
                                        <input type="radio" name="uptime" value="上午" title="上午" id="uptime" checked >
                                        <input type="radio" name="uptime" value="下午" title="下午" id="uptime">
                                        <input type="radio" name="uptime" value="晚上" title="晚上" id="uptime">
                                    </div>

                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*制作工艺：</label>
                                <div class="input-group col-sm-4">
                                    <input id="craft" type="text" class="form-control" name="craft" required="" aria-required="true" value="SLA">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*打磨要求：</label>
                                <div class="input-group col-sm-4">
                                    <input id="sanding" type="text" class="form-control" name="sanding" required="" aria-required="true" value="C">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*数量：</label>
                                <div class="input-group col-sm-2">
                                    <input id="num" type="text" class="form-control" name="num" required="" aria-required="true" value="5">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*克数：</label>
                                <div class="input-group col-sm-2">
                                    <input id="weight" type="text" class="form-control" name="weight" required="" aria-required="true" value="5">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*打印材料：</label>
                                <div class="input-group col-sm-4">
                                    <input id="material" type="text" class="form-control" name="material" required="" aria-required="true" value="白色材料">
                                </div>
                            </div>   

                           <div class="form-group">
                                <label class="col-sm-3 control-label">*注意事项：</label>
                                <div class="input-group col-sm-4">
                                    <textarea id="be_careful" name="be_careful" required lay-verify="required" placeholder="请输入" class="layui-textarea"></textarea>
                                </div>
                            </div>   
        
                              
                              <div class="form-group">
                                <label class="col-sm-3 control-label">*确认生产部：</label>
                                <div class="input-group col-sm-7">
                                    <div class="radio  col-sm-6">
                                        <input type="radio" name="confirm" value="3D打印" title="3D打印" id="confirm" checked >
                                        <input type="radio" name="confirm" value="CNC" title="CNC" id="confirm">
                                        <input type="radio" name="confirm" value="复模" title="复模" id="confirm">
                                    </div>

                                </div>
                            </div>
                             
                                                        
                                <!--                     
                             <div class="form-group">
                                <label class="col-sm-3 control-label">*确认生产部：</label>
                                 <div class="radio col-sm-4">    
                                    <input type="checkbox" value="3D打印"  id="confirm"   name="confirm[]" title="3D打印" lay-skin="primary" checked>
                                    <input type="checkbox" value="CNC机器"  id="confirm"    name="confirm[]" title="CNC机器" lay-skin="primary"> 
                                    <input type="checkbox" value="复模"  id="confirm"    name="confirm[]" title="复模" lay-skin="primary"> 

                                </div>
                              </div>  -->

                           <!-- <?php if($vo['role_id'] == 6 || $vo['role_id'] == 8 || $vo['role_id'] == 10): ?>        -->
                
                            
                            <!--  权限区域    暂时展示-->
<!--                             
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*编程人员：</label>
                                <div class="input-group col-sm-4">
                                    <input id="programmer" type="text" class="form-control" name="programmer" required="" aria-required="true">
                                </div>
                            </div>      
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*上机机号：</label>
                                <div class="input-group col-sm-4">
                                    <input id="machine_number" type="text" class="form-control" name="machine_number" required="" aria-required="true">
                                </div>
                            </div>    
                            <div class="form-group">
                              <label class="col-sm-3 control-label">*下机日期：</label>
                              <div class="input-group col-sm-2">
                                <input type="text" name="date" id="date1" autocomplete="off" class="layui-input">
                              </div>
                            </div>
 -->
                           <!-- <?php endif; ?> -->
               
                                <!--  -->
                          </div>
                          <div class="layui-tab-item form-group" >
                               <div class="layui-upload">
                                  <button type="button" class="layui-btn layui-btn-normal" id="testList" >选择多文件</button> 

                                  <div class="layui-upload-list">
                                    <table class="layui-table">
                                      <thead>
                                        <tr><th>文件名</th>
                                        <th>大小</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                      </tr></thead>
                                      <tbody id="demoList"></tbody>
                                    </table>
                                  </div>
                                  <button type="button" class="layui-btn" id="testListAction">开始上传</button>
                                </div>  

                          </div>


        
                             <div class="form-group">
                                <div class="col-sm-4 col-sm-offset-5">
                                    <!--<input type="button" value="提交" class="btn btn-primary" id="postform"/>-->
                                    <button class="btn btn-primary" id="btnSubmit"  type="submit">确认提交</button>
                                </div>
                            </div>
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
<script src="__JS__/plugins/iCheck/icheck.min.js"></script>
<script src="__JS__/plugins/bootstrap-tagsinput/bootstrap-tagsinput.min.js"></script>
<script src="__JS__/plugins/validate/jquery.validate.min.js"></script>
<script src="__JS__/plugins/validate/messages_zh.min.js"></script>
<script src="__JS__/layui/layui.js"></script>
<script src="__JS__/jquery.form.js"></script>
<script type="text/javascript" src="__JS__/jquery.flexText.js"></script>
<script type="text/javascript" src="__JS__/zoomify.min.js"></script>
<!-- <script src="__JS__/plugins/ueditor/ueditor.config.js"></script>
<script src="__JS__/plugins/ueditor/ueditor.all.js"></script> -->
<script type="text/javascript">
    
     // $(".layui-upload-file").hide();


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



    // $(document).ready(function(){
    //     $(".i-checks").iCheck({checkboxClass:"icheckbox_square-green",radioClass:"iradio_square-green",});

    // });
        // 时间
        layui.use('laydate', function(){
          var laydate = layui.laydate;
          
          //执行一个laydate实例
 

              laydate.render({
             elem: '#update' //指定元素
          });

        });

   
        

        
        //选项卡 
        layui.use('element', function(){
          var element = layui.element;
          
        });

     layui.use('upload', function(){

          var $ = layui.jquery
          // 取消掉自带的上传hidden
          

          ,upload = layui.upload;

          //多文件列表
          var demoListView = $('#demoList')
          ,uploadListIns = upload.render({
            elem: '#testList'
            ,url: "<?php echo url('order/arrayImg'); ?>"

            ,accept: 'file'
            ,multiple: true
            ,auto: false
            ,bindAction: '#testListAction'
            ,choose: function(obj){   
              var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
              //读取本地文件

              obj.preview(function(index, file, result){
                var tr = $(['<tr id="upload-'+ index +'" >'
                  ,'<td>'+ file.name +'</td>'
                  ,'<td>'+ (file.size/1014).toFixed(1) +'kb</td>'
                  ,'<td>等待上传</td>'
                  ,'<td>'
                    ,'<button class="layui-btn layui-btn-xs demo-reload layui-hide">重传</button>'
                    ,'<button class="layui-btn layui-btn-xs layui-btn-danger demo-delete">删除</button>'
                  ,'</td>'
                ,'</tr>'].join(''));
                


                //单个重传
                tr.find('.demo-reload').on('click', function(){
                  obj.upload(index, file);
                });
                
                //删除
                tr.find('.demo-delete').on('click', function(){
                  delete files[index]; //删除对应的文件
                  tr.remove();
                  uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                });

                
                demoListView.append(tr);

                // console.log(tr);

              });
            }
            ,done: function(res, index, upload){
              if(res.code == 0){ //上传成功
                var tr = demoListView.find('tr#upload-'+ index)
                ,tds = tr.children();
                // console.log(res);
                demoListView.append('<input type="hidden" name="pc_src[]" value="' + res.data.src + '"/>');

                tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                tds.eq(3).html(''); //清空操作
                return delete this.files[index]; //删除文件队列已经上传成功的文件
              }
              this.error(index, upload);
            }
            ,error: function(index, upload){
              var tr = demoListView.find('tr#upload-'+ index)
              ,tds = tr.children();
              tds.eq(2).html('<span style="color: #FF5722;">上传失败</span>');
              tds.eq(3).find('.demo-reload').removeClass('layui-hide'); //显示重传
            }
          });
        });

        


    
        // var editor = UE.getEditor('container');
    });
         layui.use('form', function(){
        
        });          

    // 表单验证
    $.validator.setDefaults({
        highlight: function(e) {
            $(e).closest(".form-group").removeClass("has-success").addClass("has-error")
            console.log(e);

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

<script type="text/javascript">
    $(function () {
        $('.content').flexText();
    });
</script>
<!--textarea限制字数-->
<script type="text/javascript">
    function keyUP(t){
        var len = $(t).val().length;
        if(len > 139){
            $(t).val($(t).val().substring(0,140));
        }
    }
</script>
<!--点击评论创建评论条-->
<script type="text/javascript">
    $('.commentAll').on('click','.plBtn',function(){
        var myDate = new Date();
        //获取当前年
        var year=myDate.getFullYear();
        //获取当前月
        var month=myDate.getMonth()+1;
        //获取当前日
        var date=myDate.getDate();
        var h=myDate.getHours();       //获取当前小时数(0-23)
        var m=myDate.getMinutes();     //获取当前分钟数(0-59)
        if(m<10) m = '0' + m;
        var s=myDate.getSeconds();
        if(s<10) s = '0' + s;
        var now=year+'-'+month+"-"+date+" "+h+':'+m+":"+s;
        //获取输入内容
        var oSize = $(this).siblings('.flex-text-wrap').find('.comment-input').val();
        console.log(oSize);
        //动态创建评论模块
        oHtml = '<div class="comment-show-con clearfix"><div class="comment-show-con-img pull-left"><img src="images/header-img-comment_03.png" alt=""></div> <div class="comment-show-con-list pull-left clearfix"><div class="pl-text clearfix"> <a href="#" class="comment-size-name">David Beckham : </a> <span class="my-pl-con">&nbsp;'+ oSize +'</span> </div> <div class="date-dz"> <span class="date-dz-left pull-left comment-time">'+now+'</span> <div class="date-dz-right pull-right comment-pl-block"><a href="javascript:;" class="removeBlock">删除</a> <a href="javascript:;" class="date-dz-z pull-left"><i class="date-dz-z-click-red"></i>赞 (<i class="z-num">666</i>)</a> </div> </div><div class="hf-list-con"></div></div> </div>';
        if(oSize.replace(/(^\s*)|(\s*$)/g, "") != ''){
            $(this).parents('.reviewArea ').siblings('.comment-show').prepend(oHtml);
            $(this).siblings('.flex-text-wrap').find('.comment-input').prop('value','').siblings('pre').find('span').text('');
        }
    });
</script>

<!--删除评论块-->
<script type="text/javascript">
    $('.commentAll').on('click','.removeBlock',function(){
        var oT = $(this).parents('.date-dz-right').parents('.date-dz').parents('.all-pl-con');
        if(oT.siblings('.all-pl-con').length >= 1){
            oT.remove();
        }else {
            $(this).parents('.date-dz-right').parents('.date-dz').parents('.all-pl-con').parents('.hf-list-con').css('display','none')
            oT.remove();
        }
        $(this).parents('.date-dz-right').parents('.date-dz').parents('.comment-show-con-list').parents('.comment-show-con').remove();

    })
</script>
<!--点赞-->
<script type="text/javascript">
    $('.comment-show').on('click','.date-dz-z',function(){
        var zNum = $(this).find('.z-num').html();
        if($(this).is('.date-dz-z-click')){
            zNum--;
            $(this).removeClass('date-dz-z-click red');
            $(this).find('.z-num').html(zNum);
            $(this).find('.date-dz-z-click-red').removeClass('red');
        }else {
            zNum++;
            $(this).addClass('date-dz-z-click');
            $(this).find('.z-num').html(zNum);
            $(this).find('.date-dz-z-click-red').addClass('red');
        }
    })
</script>
</body>
</html>
