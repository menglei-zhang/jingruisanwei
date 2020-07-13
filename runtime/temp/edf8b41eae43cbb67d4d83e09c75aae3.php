<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:86:"/Applications/MAMP/htdocs/www/jingruisanwei/application/admin/view/order/orderadd.html";i:1564544286;}*/ ?>
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
        #p-top .layui-form-radio {
            margin-top: 3px;
        }

        #program_box {
            display: none;
        }
    </style>
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>添加订单</h5>
                     <hr>
                    <h5 style="color:red">*注释：1.上传文件时先点击选择多图片->开始上传->确定提交  2.如提交后页面一直在加载请选择常用操作里的刷新刷新从填  3.点了提交没反应时请确认订单填写页面是否有未填写信息</h5>
             
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
                                    <input id="order_sn" type="text" class="form-control" name="order_sn" required="" aria-required="true" value="<?php echo $orderSn; ?>">
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
                                      <select name="username" lay-verify="required" id="username" lay-search    >
                                        <option value=""></option>
                                      <?php if(is_array($name) || $name instanceof \think\Collection || $name instanceof \think\Paginator): $k = 0; $__LIST__ = $name;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$name): $mod = ($k % 2 );++$k;?>
                                        
                                        <option value="<?php echo $name['cust_name']; ?>" ><?php echo $name['cust_name']; ?> - <?php echo $name['cust_liaison']; ?></option>
                            
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

                         <!-- <?php if($vo['role_id'] != 4): ?>        -->

                              <div class="form-group">
                                <label class="col-sm-3 control-label">*业务员名称：</label>
                                <div class="input-group col-sm-4">
                                   <input id="workname" type="text" class="form-control" name="workname" required="" aria-required="true" value="<?php echo $vo['real_name']; ?>">
                                </div>
                            </div>
                        <!-- <?php endif; ?> --> 
                        <!-- <?php if($vo['role_id'] == 4): ?>        -->

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
                            
                            <!-- <?php endif; ?> --> 


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
                                        <input type="radio" name="uptime" value="上午" title="上午(8:30-11:30)" id="uptime" checked >
                                        <input type="radio" name="uptime" value="下午" title="下午(11:30 - 17:00)" id="uptime">
                                        <input type="radio" name="uptime" value="晚上" title="晚上(17:00-20:00))" id="uptime">
                                    </div>

                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">制作工艺：</label>
                                <div class="input-group col-sm-4">
                                    <input id="craft" type="text" class="form-control" name="craft" aria-required="true" value="SLA">
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">打磨要求：</label>
                                <div class="input-group col-sm-4">
                                    <input id="sanding" type="text" class="form-control" name="sanding" aria-required="true" value="C">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">数量：</label>
                                <div class="input-group col-sm-2">
                                    <input id="num" type="text" class="form-control" name="num" aria-required="true" value="5">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">克数：</label>
                                <div class="input-group col-sm-2">
                                    <input id="weight" type="text" class="form-control" name="weight" aria-required="true" value="5">
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
                                   <label class="col-sm-3 control-label">*确认分配方式：</label>
                                   <div class="input-group col-sm-7">
                                       <div class="radio  col-sm-6" style="padding-top: 0; padding-left: 0;" id="p-top">
                                           <input type="radio" name="type" value="0" title="随机分配" lay-filter="type" >

                                           <input type="radio" name="type" value="1" title="指定人员" lay-filter="type" >
                                       </div>
                                   </div>
                               </div>
        
                              
                              <div class="form-group">
                                <label class="col-sm-3 control-label">*确认生产部：</label>
                                <div class="input-group col-sm-7">


                                    <div class="radio  col-sm-6" style="padding-top: 0; padding-left: 0;" id="p-top">
                                        <?php if(is_array($shenchan) || $shenchan instanceof \think\Collection || $shenchan instanceof \think\Paginator): $k = 0; $__LIST__ = $shenchan;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shenchan): $mod = ($k % 2 );++$k;?>
                                         <input type="radio" name="confirm" value="<?php echo $shenchan['id']; ?>" title="<?php echo $shenchan['group_name']; ?>" id="confirm" lay-filter="confirm"  >
                                        <!--  <input type="radio" name="confirm" value="CNC" title="CNC" id="confirm">
                                         <input type="radio" name="confirm" value="复模" title="复模" id="confirm"> -->
                                         <?php endforeach; endif; else: echo "" ;endif; ?> 
                                    </div>

                                </div>
                            </div>

                               <div class="form-group" id="program_box">
                                   <label class="col-sm-3 control-label">*编程人员：</label>

                                   <div class="input-group col-sm-4">
                                       <select name="program" lay-verify="required" id="program" lay-search    >

                                       </select>
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
<script src="__JS__/md5.js"></script>
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

          let uploadData = [];
          let filesResource;

          //多文件列表
          var demoListView = $('#demoList')
          ,uploadListIns = upload.render({
            elem: '#testList'
            ,url: ""
            ,accept: 'file'
            ,multiple: true
            ,auto: false
            ,bindAction: ''
            ,choose: function(obj){
              $("input[name='file']").val('');
              
              uploadData = [];
              $("#demoList tr").each(function (i, v){
                  console.log($(v).data('isedit'));
                  if(!$(v).data('edit')){
                        $(v).remove();
                  }
              });

              var files = this.files = obj.pushFile(); //将每次选择的文件追加到文件队列
              filesResource = files;

                  // 文件详情
              let index, file, currentIndex = 0;
              for(let i in files){
                  index = i, file = files[i];

                  var name = file.name,        //文件名
                      size = file.size;        //总大小

                  var shardSize = 5 * 1024 * 1024,     //以5MB为一个分片
                      shardCount = Math.ceil(size / shardSize);   //总片数

                  let tempName = $.md5(file.name + random(1, 10000));
                  let ext = '.yu';

                  // 当前文件切片
                  let tempData = {
                      tempName: index,
                      list: []
                  };
                  for (let n = 0; n < shardCount; ++n) {
                      //计算每一片的起始与结束位置
                      var start = n * shardSize,
                          end = Math.min(size, start + shardSize);

                      var objf = file.slice(start, end);
                      let fileName = $.md5('' + random(1, 10000) + n) + '_' + n + ext;

                      //构造一个表单，FormData是HTML5新增的
                      var form = new FormData();
                      form.append('file', objf, fileName);
                      form.append('index', n);
                      form.append('fileName', file.name);
                      form.append('tempName', tempName);
                      form.append('totalBockNum', shardCount - 1);

                      tempData.list.push(form);
                  }
                  uploadData.push(tempData);

                  var tr = $(['<tr id="upload-'+ index +'" data-name="'+ tempName +'" data-index="'+ index +'" data-i="'+ currentIndex +'">'
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
                        let x = $(this).parents('tr').data('index');
                        delete files[x]; //删除对应的文件

                        let temp = [];
                        for(let i in uploadData){
                            if(uploadData[i].tempName != x){
                                temp.push(uploadData[i]);
                            }
                        }
                        uploadData = temp;

                        $(this).parents('tr').remove();
                      uploadListIns.config.elem.next()[0].value = ''; //清空 input file 值，以免删除后出现同名文件不可选
                    });

                  demoListView.append(tr);
                  currentIndex++;
              }
            }
            ,done: function(res, index, upload){}
            ,error: function(index, upload){}
          });

          let click = true;
          // 上传文件
          $("#testListAction").click(function (){
              if(uploadData.length == 0){
                  layer.alert('请先选择文件');
                  return;
              }

              if(!click) {
                  layer.alert('正在上传，请勿重复点击');
                  return;
              }
              click = false;

             for(let i in uploadData){
                 let list = uploadData[i].list;
                 let index = uploadData[i].tempName;

                 for(let n in list){
                     try{
                         // Ajax提交
                         $.ajax({
                             url: "<?php echo url('upload/maxFile'); ?>",
                             type: "POST",
                             data: list[n],
                             async: true,         //异步
                             processData: false,  //很重要，告诉jquery不要对form进行处理
                             contentType: false,  //很重要，指定为false才能形成正确的Content-Type
                             success: function (res) {
                                 console.log(res);
                                 if(2 == res.data.code){
                                     $("#demoList tr").each(function (i, v){
                                         if(res.data.fileName == $(v).data('name')){
                                             $(v).find('td').eq(2).html('上传成功');
                                             $(v).find('td').eq(2).css('color', 'green');

                                             $(v).append('<input type="hidden" name="pc_src[]" id="pc_src" value="' + res.data.path + '"/>');
                                             $(v).data('edit', '1');
                                         }
                                     });
                                     click = true;

                                     // 删除
                                     delete filesResource[index];

                                     let temp = [];
                                     for(let i in uploadData){
                                         if(uploadData[i].tempName != index){
                                             temp.push(uploadData[i]);
                                         }
                                     }
                                     uploadData = temp;
                                 } else if(0 == res.data.code){
                                     layer.alert(res.msg);
                                 }
                             }
                         });
                     } catch (e) {
                         console.log(e);

                         $("#testListAction").html('重新上传');
                        click = true;
                     }
                 }
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

         /**
      * 随机数
      */
     function random (m,n){
         return Math.floor(Math.random()*(m - n) + n);
     }
    
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


<script>
    layui.use('form', function (){
        let form = layui.form;

        form.on('radio(type)', function (data){
            if(1 == data.value) {
                let confirm = $("input[name='confirm']:checked").val();

                if('undefined' == typeof confirm) return;

                programData({'confirm': confirm}, form);
            } else {
                $('#program_box').hide();
            }
        });

        form.on('radio(confirm)', function (data){
            let type = $("input[name='type']:checked").val();

            if(1 == type){
                programData({'confirm': data.value}, form);
            }
        });
    });

    // 展示编辑人员
    function programData(data, form){
        $.ajax({
            url: '<?php echo url("Order/programList"); ?>',
            type: 'POST',
            data,
            dataType: 'json',
            success(res){
                if(500 == res.code) {
                    // 隐藏当前模块
                    $('#program_box').hide();
                    $('#program').html('');

                    layer.msg(res.msg);

                    return;
                }

                // 展示当前模块
                $('#program_box').show();

                // 添加相关的编辑人员
                let html = '<option value="">请选择编程人员</option>';
                for(let item of res.data.list){
                    html += '<option value="'+ item.user_id +'">'+ item.user_name +'</option>';
                }
                $('#program').html(html);
                form.render('select');
            }
        });
    }
</script>
</body>
</html>
