<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:58:"/webdata/snake/application/admin/view/order/orderedit.html";i:1558943070;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>编辑订单</title>
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
    <style type="text/css">
       .commentAll .comment-show .comment-show-con .comment-show-con-list .example { float: left; margin: 10px 14px;};
        .zoomify { cursor: pointer; cursor: -webkit-zoom-in; cursor: zoom-in; }
      .zoomify.zoomed { cursor: -webkit-zoom-out; cursor: zoom-out; padding: 0; margin: 0; border: none; border-radius: 0; box-shadow: none; position: relative; z-index: 1501; }
      .zoomify-shadow { position: fixed; top: 0; left: 0; right: 0; bottom: 0; width: 100%; height: 100%; display: block; z-index: 1500; background: rgba(0, 0, 0 , .3); opacity: 0; }
      .zoomify-shadow.zoomed { opacity: 1; cursor: pointer; cursor: -webkit-zoom-out; cursor: zoom-out; }


    </style>

</head>
<body class="gray-bg">
<div class="wrapper wrapper-content animated fadeInRight">
    <div class="row">
        <div class="col-sm-10">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>修改订单</h5>
                     <hr>
                    <h5 style="color:red">*注释：1.上传文件时因涉及到服务器文件安全问题，上传的文件名均要进行加密处理 2.删除文件请删除后点击确定提交才可以生效 3.为防止留言删除误操作，订单留言留言删除请放到对应要删除的留言，删除按钮才会显示</h5>
                </div>
                <div class="ibox-content">
                    <form class="layui-form form-horizontal m-t " id="commentForm" method="post" action="<?php echo url('order/orderedit'); ?>" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $content['id']; ?>"/>
                    <div class="layui-tab">
                      <ul class="layui-tab-title">
                        <li class="layui-this">订单填写</li>
                        <li>文件图片上传</li>
                         <li>订单留言</li>  
                        </ul>
                        <div class="layui-tab-content">
                           <div class="layui-tab-item layui-show">
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*订单编号：</label>
                                <div class="input-group col-sm-4">
                                    <input id="order_sn" type="text" class="form-control" name="order_sn" required="" aria-required="true" value="<?php echo $content['order_sn']; ?>"  <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>
                            <?php if($vo['role_id'] == '1' or $vo['role_id'] == '3' or $vo['role_id'] == '4' or $vo['role_id'] == '5' or $vo['role_id'] == 13): ?>
                                
                             <!-- 测试版本 -->
                      <div class="form-group">
                                <label class="col-sm-3 control-label ">*客户名称：</label>

                                    <div class="input-group col-sm-4">
                                      <select name="username" lay-verify="required" id="username" lay-search> 
                                        <option value=""></option>
                                      <?php if(is_array($name) || $name instanceof \think\Collection || $name instanceof \think\Paginator): $k = 0; $__LIST__ = $name;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$name): $mod = ($k % 2 );++$k;?>
                                       

                                        <option value="<?php echo $name['cust_name']; ?>" <?php if($name['cust_name'] == $content['username']): ?>selected <?php endif; ?>><?php echo $name['cust_name']; ?></option>
                            
                                      <?php endforeach; endif; else: echo "" ;endif; ?>
                                      </select>
                                    </div>
                            </div>


                                
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*订单金额：</label>
                                <div class="input-group col-sm-4">
                                    <input id="place" type="text" class="form-control" name="place" required="" aria-required="true" value="<?php echo $content['place']; ?>">
                                </div>
                            </div>
                            <?php endif; ?>

                            
                            <!-- 测试版本 -->
                              <div class="form-group">
                                <label class="col-sm-3 control-label">*业务员名称：</label>
                                <div class="input-group col-sm-4">
                                    <input id="workname" type="text" class="form-control" name="workname" required="" aria-required="true" value="<?php echo $content['workname']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>

                            

                            <!--正式版本  -->
  <!--                           <div class="form-group">
                                <label class="col-sm-3 control-label">*业务员名称：</label>

                                    <div class="input-group col-sm-4">
                                      <select name="workname" lay-verify="required" id="workname">
                                        <option value=""></option>
                                     
                                        <option value="<?php echo $vo['real_name']; ?>" ><?php echo $vo['real_name']; ?></option>
                                    
                                      </select>
                                    </div>
                            </div> -->
                           
                            <div class="form-group">
                              <label class="col-sm-3 control-label">*交货日期：</label>
                              <div class="input-group col-sm-2">
                                <input type="text" name="update" id="update" autocomplete="off" class="layui-input" value="<?php echo $content['update']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                              </div>
                            </div>
                            

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*交货时间：</label>
                                <div class="input-group col-sm-7">
                                    <div class="radio  col-sm-6">
                                     
                                        <input type="radio" name="uptime" value="上午" title="上午" id="uptime"  <?php if($content['uptime'] == '上午'): ?> checked <?php endif; if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                    
                                        <input type="radio" name="uptime" value="下午" title="下午" id="uptime" <?php if($content['uptime'] == '下午'): ?> checked <?php endif; if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>

                                        <input type="radio" name="uptime" value="晚上" title="晚上" id="uptime" <?php if($content['uptime'] == '晚上'): ?> checked <?php endif; if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                    
                                    </div>

                                </div>
                            </div>
                            <?php if($vo['role_id'] != '13'): ?>
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*制作工艺：</label>
                                <div class="input-group col-sm-4">
                                    <input id="craft" type="text" class="form-control" name="craft" required="" aria-required="true" value="<?php echo $content['craft']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*打磨要求：</label>
                                <div class="input-group col-sm-4">
                                    <input id="sanding" type="text" class="form-control" name="sanding" required="" aria-required="true" value="<?php echo $content['sanding']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*数量：</label>
                                <div class="input-group col-sm-2">
                                    <input id="num" type="text" class="form-control" name="num" required="" aria-required="true" value="<?php echo $content['num']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-sm-3 control-label">*克数：</label>
                                <div class="input-group col-sm-2">
                                    <input id="weight" type="text" class="form-control" name="weight" required="" aria-required="true" value="<?php echo $content['weight']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*打印材料：</label>
                                <div class="input-group col-sm-4">
                                    <input id="material" type="text" class="form-control" name="material" required="" aria-required="true" value="<?php echo $content['material']; ?>" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>
                                </div>
                            </div>   

                           <div class="form-group">
                                <label class="col-sm-3 control-label">*注意事项：</label>
                                <div class="input-group col-sm-4">
                                    <textarea id="be_careful" name="be_careful" required lay-verify="required" placeholder="请输入" class="layui-textarea"  <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?> ><?php echo $content['be_careful']; ?></textarea>
                                </div>
                            </div>   
                            
                                       
<!--                              <div class="form-group">
                                <label class="col-sm-3 control-label">*确认生产部：</label>
                                 <div class="radio col-sm-4">    
       
                                    <input type="checkbox" value="3D打印"  id="confirm"   name="confirm[]" title="3D打印" lay-skin="primary" <?php if($content['confirm'] == '3D打印'): ?> checked <?php endif; ?> >
                                    <input type="checkbox" value="CNC机器"  id="confirm"    name="confirm[]" title="CNC机器" lay-skin="primary" <?php if($content['confirm'] == 'CNC机器'): ?> checked <?php endif; ?>> 
                                    <input type="checkbox" value="复模"  id="confirm"    name="confirm[]" title="复模" lay-skin="primary" <?php if($content['confirm'] == '复模'): ?> checked <?php endif; ?>> 

                                </div>
                              </div>  -->
                              <input type="hidden" name="confirm" value="<?php echo $content['confirm']; ?>"/>                                    
                           <?php if($vo['role_id'] != '8' &&  $vo['role_id'] != '6' && $vo['role_id'] != 10  &&  $vo['role_id'] != 7 &&  $vo['role_id'] != 9 &&  $vo['role_id'] != 11 && $vo['role_id'] != 12): ?> 

                          <div class="form-group">
                                <label class="col-sm-3 control-label">*确认生产部：</label>
                                <div class="input-group col-sm-7">
                                    <div class="radio  col-sm-6">
                                        <?php if(is_array($shenchan) || $shenchan instanceof \think\Collection || $shenchan instanceof \think\Paginator): $k = 0; $__LIST__ = $shenchan;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$shenchan): $mod = ($k % 2 );++$k;?>
                                         <input type="radio" name="confirm" value="<?php echo $shenchan['id']; ?>" title="<?php echo $shenchan['group_name']; ?>" id="confirm"  <?php if($content['confirm'] == $shenchan['id']): ?>checked<?php endif; ?> >
                        
                                         <?php endforeach; endif; else: echo "" ;endif; ?> 
                                    </div>

                                </div>
                            </div>
                           <?php endif; ?>
                           <!-- <?php if($vo['role_id'] == 6 || $vo['role_id'] == 8 || $vo['role_id'] == 10 || $vo['role_id'] == 1 ||  $vo['role_id'] == 7 ||  $vo['role_id'] == 9 ||  $vo['role_id'] == 11 ||  $vo['role_id'] == 12): ?>        -->
                
                              
                            <!--  权限区域    暂时展示-->
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*编程人员：</label>
                                <div class="input-group col-sm-4">
                                    <input id="programmer" type="text" class="form-control" name="programmer" required="" aria-required="true" value="<?php echo $content['programmer']; ?>" >
                                </div>
                            </div>      
                            
                            <div class="form-group">
                                <label class="col-sm-3 control-label">*上机机号：</label>
                                <div class="input-group col-sm-4">
                                    <input id="machine_number" type="text" class="form-control" name="machine_number" required="" aria-required="true" value="<?php echo $content['machine_number']; ?>">
                                </div>
                            </div>    
                            <div class="form-group">
                              <label class="col-sm-3 control-label">*下机日期：</label>
                              <div class="input-group col-sm-2">
                                <input type="text" name="date" id="date1" autocomplete="off" class="layui-input" value="<?php echo $content['date']; ?>">
                              </div> 
                            </div>
          
                           <!-- <?php endif; ?> -->
                           <?php endif; ?>
                            <div class="form-group">
                              <label class="col-sm-3 control-label" style="color:red">*超出多少小时</label>
                              <div class="input-group col-sm-2">
                                <input type="text" class="layui-input" value="<?php echo $content['overtime']; ?>" style="color:red">
                              </div> 
                            </div>
                               
                          </div>
                          <div class="layui-tab-item form-group" >
                               <div class="layui-upload">
                               <?php if($vo['role_id'] == '1' || $vo['role_id'] == '3' || $vo['role_id'] == '4'): ?>

                                  <button type="button" class="layui-btn layui-btn-normal" id="testList" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>选择多文件</button> 
                                <?php endif; ?>
                                  <div class="layui-upload-list">
                                    <table class="layui-table">
                                      <thead>
                                        <tr><th>文件名</th>
                                        <th>大小</th>
                                        <th>状态</th>
                                        <th>操作</th>
                                      </tr></thead>
                                      <tbody id="demoList">
                                          
                                      </tbody>
                                    </table>
                                  </div>
                                   <?php if($vo['role_id'] == '1' || $vo['role_id'] == '3' || $vo['role_id'] == '4'): ?>
                                  <button type="button" class="layui-btn" id="testListAction" <?php if($vo['role_id'] != '1' and $vo['role_id'] != '3' and $vo['role_id'] != '4'): ?>disabled="disabled"<?php endif; ?>>开始上传</button>
                                  <?php endif; ?>
                                </div>  

                          </div>
                            
                          
                          <div class="layui-tab-item form-group" >
                                
                            
                              <div class="commentAll">
                                <!--评论区域 begin-->
                                <div class="reviewArea clearfix">
                                    <textarea class="content comment-input" placeholder="Please enter a comment&hellip;" onkeyup="keyUP(this)"></textarea>
                                    <a href="javascript:;" class="plBtn">评论</a>

                           <!--        <button type="button" class="layui-btn layui-btn layui-btn-radius" id="mess_img" style="float: right;margin: 19px;"> 
                                      <i class="layui-icon">&#xe67c;</i>上传图片
                                  </button> -->

                                  <button type="button" class="layui-btn"  id="mess_img" style="float: right;margin: 18px;">多图片上传</button> 
                                      <blockquote class="layui-elem-quote layui-quote-nm" style="margin-top: 75px;">
                                        预览图：
                                        <div class="layui-upload-list" id="demo2"></div>
                                     </blockquote>

                                </div>
                                <!--评论区域 end-->
                                <!--回复区域 begin-->
                                <div class="comment-show">
                                     <?php foreach($message as $mess): ?> 
                                    <div class="comment-show-con clearfix">
                                                                                                          
                                      <div class="comment-show-con-img pull-left"><img src="<?php echo $mess['head']; ?>" alt="" style="width: 48px;height: 48px;"></div>
                                        <div class="comment-show-con-list pull-left clearfix">
                                            <div class="pl-text clearfix">
                                                <a href="#" class="comment-size-name"><?php echo $mess['real_name']; ?>：</a>
                                                <span class="my-pl-con">&nbsp;<?php echo $mess['mess_list']; ?></span>
                                            </div>
                                            <div class="date-dz">
                                                <span class="date-dz-left pull-left comment-time"><?php echo $mess['add_time']; ?></span>
                                                <div class="date-dz-right pull-right comment-pl-block">
                                                
                                                      
                                                    <?php if($vo['user_id'] == $mess['user_id']): ?>

                                                        <a href="javascript:;" class="removeBlock" messid ="<?php echo $mess['mess_id']; ?>">删除</a>

                                                    <?php endif; ?>        
                                                    
                                                
                                                    <!--  ---------------------------回复和点赞功能----------------------------- -->
                                                    <!-- <a href="javascript:;" class="date-dz-pl pl-hf hf-con-block pull-left">回复</a> -->
                                                     <!--   <span class="pull-left date-dz-line">|</span> -->
                                                    <!-- <a href="javascript:;" class="date-dz-z pull-left"><i class="date-dz-z-click-red"></i>赞 (<i class="z-num">666</i>)</a> -->
                                                     <!-- ---------------------------------------------------------- -->

                                                </div>
                                            </div>
                                            

                                            <?php foreach($mess['mess_img'] as $img): ?>

                                              <!-- <div class="message_img"><img src="<?php echo $img; ?>" style="height: 60px;"></div> -->

                                                    <div class="example ">
                                                      <img src="<?php echo $img; ?>" class="img-rounded" alt="" style="width:70px">
                                                    </div>                             
                                              
                                            <?php endforeach; ?>
                                
                                            <div class="hf-list-con"></div>
                                        </div>

                                        
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <!--回复区域 end-->
                            </div>
                              

                          </div>  
                             <div class="form-group">   
                               <?php switch($vo['role_id']): case "1":case "3": ?>
                                <div class="col-sm-4 col-sm-offset-5">
                                    <input type="hidden" name="role_id" value="<?php echo $vo['role_id']; ?>"/>
                                   <button class="btn btn-primary" id="btnSubmit"  type="submit" lay-filter="btnSubmit">确认提交</button>
                                </div>
                                <?php break; case "4": ?>
                                <div class="col-sm-4 col-sm-offset-5">
                                    <input type="hidden" name="static" value="2"/>
                                     <input type="hidden" name="role_id" value="<?php echo $vo['role_id']; ?>"/>
                                     <button class="btn btn-primary" id="btnSubmit"  type="submit" lay-filter="btnSubmit">确认提交</button>
                                    <a class="btn btn-primary" id="escSubmit"  onclick="escSubmit(<?php echo $content['id']; ?>,'3')">返回订单</a>
                                </div> 
                                <?php break; case "6":case "8":case "10": ?>
                                <div class="col-sm-4 col-sm-offset-5">
                                    <input type="hidden" name="static" value="4"/>
                                    <input type="hidden" name="role_id" value="<?php echo $vo['role_id']; ?>"/>
                                   <button class="btn btn-primary" id="btnSubmit"  type="submit" lay-filter="btnSubmit">确认提交</button>
                                </div>
                                <?php break; case "7":case "9":case "11": ?>
                                <div class="col-sm-4 col-sm-offset-5">
                                    <input type="hidden" name="static" value="5"/>
                                     <input type="hidden" name="role_id" value="<?php echo $vo['role_id']; ?>"/>
                                     <button class="btn btn-primary" id="btnSubmit"  type="submit" lay-filter="btnSubmit">确认提交</button>
                                    <a class="btn btn-primary" id="escSubmit"  onclick="escSubmit(<?php echo $content['id']; ?>,'2')">返回订单</a>
                                </div> 
                                <?php break; endswitch; ?>
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
<script type="text/javascript" src="__JS__/browser-md5-file.min.js"></script>
<script type="text/javascript" src="__JS__/jquery.json.min.js"></script>
<!-- <script src="__JS__/plugins/ueditor/ueditor.config.js"></script>
<script src="__JS__/plugins/ueditor/ueditor.all.js"></script> -->
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


        // var editor = UE.getEditor('container');
    });
  
        // -------------------------------
        //  多文件分片上传
        // ------------------------------- 

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
              uploadData = [];
              // $("#demoList tr").each(function (i, v){
              //     console.log($(v).data('isedit'));
              //     if(!$(v).data('edit')){
              //           $(v).remove();
              //     }
              // });

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
             var demoListView = $('#demoList')

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
                                           var tr = demoListView.find('tr#upload-'+ index)
                                            ,tds = tr.children();
                                             // $(v).find('td').eq(2).html('上传成功');
                                             // $(v).find('td').eq(2).css('color', 'green');
                                             // $(v).append('<input type="hidden" name="pc_src[]" id="pc_src" value="' + res.data.path + '"/>');
                                             demoListView.append('<input type="hidden" name="pc_src[]" id="pc_src" value="' + res.data.path + '"/>');
                                              tds.eq(2).html('<span style="color: #5FB878;">上传成功</span>');
                                             tds.eq(3).html(''); //清空操作
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

        }) ;
    
        
        // ----------------------
        //    留言图片上传 
        // ----------------------
        layui.use('upload', function(){

          var $ = layui.jquery
          // 取消掉自带的上传hidden
          
          ,upload = layui.upload

         ,uploadListIns = upload.render({
            elem: '#mess_img'
            ,url: "<?php echo url('order/arrayImg'); ?>"
            ,multiple: true
            ,before: function(obj){
              //预读本地文件示例，不支持ie8
              obj.preview(function(index, file, result){
              // console.log(result);

                $('#demo2').append('<img src="'+ result +'" value="'+ file.name +'" class="layui-upload-img"  width="100px" height="100px" style="margin: 10px;">')
              });
            }
            ,done: function(res){


                     $('#demo2').append('<input type="hidden" name="mess_img[]" id="mess_img" value="' + res.data.src + '"/>');



            }
          });
          
       });



              // 双击图片放大
         $('.example img').zoomify();


        layui.use('element', function(){
          var element = layui.element;
          
        });

         layui.use('laydate', function(){
          var laydate = layui.laydate;
          
          //执行一个laydate实例
          laydate.render({
            elem: '#date1' //指定元素   
             ,type: 'datetime'  
          });

              laydate.render({
             elem: '#update' //指定元素
          });

        });

      layui.use('form', function(){



     }); 


      layui.use('flow', function () {
            var $ = layui.jquery; //不用额外加载jQuery，flow模块本身是有依赖jQuery的，直接用即可。
            var flow = layui.flow;
            var list = $('#demoList');
            flow.load({
                elem: '#demoList'    //指定列表容器
                , isAuto: true      //到底页面底端自动加载下一页，设为false则点击'加载更多'才会加载
                //, mb: 100          //距离底端多少像素触发auto加载
                , isLazying: true    //当单个li很长时，内部有很多图片，对图片进行懒加载，默认false。
                // , end: '<p style="color:red">木有了</p>'    //加载所有后显示文本，默认'没有更多了'
                , done: function (page, next) {            //到达临界，触发下一页
                    var a ;
                    var lis = [];
                    a = "<?php echo $content['pc_src']; ?>".split(",");

                      console.log(a);  

                  layui.each(a, function(index, item){
                      
                       
                       if(a.length == 1){

                          // console.log(a[0]);
                          if(a[0] == ''){

                              console.log('数组为空');
                          }else{
                              
                             index = index + 1;
                          }

                       }else{
                          index = index + 1;

                       } 
                    
                       var fileExt=(/[.]/.exec(item)) ? /[^.]+$/.exec(item.toLowerCase()) : '';

                       // console.log(fileExt);

                      if(index > 0){

                          if(fileExt != 'jpg'){

                             lis.push(['<tr id="upload-'+ index +'" >' ,

                               '<td>'+ item +'</td>'       
                              ,'<td>'+ random(1,5000) +'</td>'
                              ,'<td>已上传</td>'
                              ,'<td><?php if($vo['role_id'] == 3 || $vo['role_id'] == 4): ?><a class="layui-btn layui-btn-xs layui-btn-danger demo-delete" onclick=urldelete("' + item + '")>删除</a><?php endif; ?><a class="layui-btn layui-btn-xs layui-btn-danger demo-delete" onclick=urlupload("' + item + '")  target="_blank">下载</a></td>'
                            ,'</tr>']);
                            
                         } else{

                           lis.push(['<tr id="upload-'+ index +'" >' ,

                               '<td><img src = "'+ item +'"></td>'         
                            ,'<td>'+ random(1,5000) +'</td>'
                            ,'<td>已上传</td>'
                            ,'<td><?php if($vo['role_id'] == 3 || $vo['role_id'] == 4): ?><a class="layui-btn layui-btn-xs layui-btn-danger demo-delete" onclick=urldelete("' + item + '")>删除</a><?php endif; ?><a class="layui-btn layui-btn-xs layui-btn-danger demo-delete" onclick=urlupload("' + item + '")  target="_blank">下载</a></td>'
                          ,'</tr>']);
                            

                         } 

                    } 
                                                    

                    
                    }); 

                  

                    lis.push('<input type="hidden" name="pc_src[]" id="pc_src" value="' + a + '"/>');

                    page = a.length;

                    next(lis.join(''), page < a.length);//pages是后台返回的总页数

        

                    // });
                }
            });
        });
        
        // 返回订单按钮
        function escSubmit(id,staticlist){

        $.ajax({
          url: "<?php echo url('order/editstatic'); ?>",
          type: 'post',
          data: {"id": id,"static": staticlist},
          dataType: 'json',
          timeout: 1000,
          success: function (data, status) {
                  
                  layer.msg('返回成功');

          },
          fail: function (err, status) {
            console.log(err)
          }
        })

    }


    //删除
    function urldelete(item){
      

       var a = $('#pc_src').val().split(",");

        $("[id^='upload']").click(function(){    
             this.remove();
        }); 

       var index = a.indexOf(item);

        a.splice(index, 1);

        var arr = a.join(',');

        // console.log(arr);

         $("#pc_src").val(arr);

    }   

    // 下载
    function urlupload(item){

        

         $.ajax({
          url: "<?php echo url('order/downloadlist'); ?>",  
          type: 'post',
          data: {"download": item},
          dataType: 'json',
          success: function (data) {
                

            window.open(data,'width=600,height=400,top=100px,left=0px')

          },
          fail: function (err, status) {
            console.log(err)
          }
        })



    }



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

<!-- 留言代码 start -->

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
        
        var real_name = '<?php echo $vo['real_name']; ?>'

        var head = '<?php echo $vo['head']; ?>'


        //动态创建评论模块
        oHtml = '<div class="comment-show-con clearfix"><div class="comment-show-con-img pull-left"><img src="' + head + '" alt="" style="width: 48px;height: 48px;"></div> <div class="comment-show-con-list pull-left clearfix"><div class="pl-text clearfix"> <a href="#" class="comment-size-name">' + real_name + '：</a> <span class="my-pl-con">&nbsp;'+ oSize +'</span> </div> <div class="date-dz"> <span class="date-dz-left pull-left comment-time">' + now + '</span> <div class="date-dz-right pull-right comment-pl-block"> <a href="javascript:;" class="removeBlock" >删除</a></div></div><div class="hf-list-con"></div>';

        // 将评论和当前评论时间


           var arr = [];

           var x ;


                //根据name的值获取到所有，并遍历
          $("#demo2 input:hidden[id='mess_img']").each(function(i){
                    //arr.push($(this).val());
            arr[i] = $(this).val();

            //循环拼接图片 
             x  = '<div class="example "><img src="' + arr[i] + '" class="img-rounded" style="width:70px; "></div>' ;

             oHtml = oHtml + x;

        });  


            y = '</div></div>'

            oHtm = oHtml + y;

             console.log(oHtm)

           

             arr = arr.join(',');

        $.ajax({
          url: "<?php echo url('order/addmessage'); ?>",
          type: 'post',
          data: {"user_id": <?php echo $vo['user_id']; ?>,"mess_list": oSize,"add_time": now, "order_id": <?php echo $content['id']; ?>, "real_name": real_name, "head": head, "mess_img": arr},
          dataType: 'json',
          success: function (data) {
              console.log(data)
          },
          fail: function (err, status) {
            console.log(err)
          }
        })

        if(oSize.replace(/(^\s*)|(\s*$)/g, "") != ''){
            $(this).parents('.reviewArea ').siblings('.comment-show').prepend(oHtm);
            $(this).siblings('.flex-text-wrap').find('.comment-input').prop('value','').siblings('pre').find('span').text('');
        }

          $('.example img').zoomify();  
    });
      
     

</script>

<!--删除评论块-->
<script type="text/javascript">
    $('.commentAll').on('click','.removeBlock',function(){

        var oT = $(this).parents('.date-dz-right').parents('.date-dz').parents('.all-pl-con');

        // 自定义标签

        var id = $(this).attr("messid");

        
        if(oT.siblings('.all-pl-con').length >= 1){
            oT.remove();
        }else {

            $(this).parents('.date-dz-right').parents('.date-dz').parents('.all-pl-con').parents('.hf-list-con').css('display','none')
            oT.remove();
        }
        $(this).parents('.date-dz-right').parents('.date-dz').parents('.comment-show-con-list').parents('.comment-show-con').remove();

        $.ajax({
          url: "<?php echo url('order/delmessage'); ?>",
          type: 'post',
          data: {"mess_id": id},
          dataType: 'json',
          success: function (data) {
              console.log(data)
          },
          fail: function (err, status) {
            console.log(err)
          }
        })


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
