<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:53:"/webdata/snake/application/admin/view/view/index.html";i:1559020915;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>文章列表</title>
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
            <h5>消息</h5>
        </div>
            <div class="example-wrap">
                <h2>订单消息<h2>
                <ul>
                    
                    <?php if(is_array($viewopen['order']) || $viewopen['order'] instanceof \think\Collection || $viewopen['order'] instanceof \think\Paginator): $i = 0; $__LIST__ = $viewopen['order'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li  id="li_0" class="msg-read" style="position: relative;padding: 16px 0;border-bottom: 1px solid #e0e0e0;list-style: none;">
                            
                        <p  class="msg-text clearfix" style="display: block;margin-top: 8px;padding: 0;color: #4d4d4d;">

                       
                            <span  class="bb-span-wrap" style="width: 80%;font-weight: 700;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">
                                
                                <a href="<?php echo url('admin/order/orderedit'); ?>?id=<?php echo $vo['id']; ?>&is_view=<?php echo $upload_view; ?>&is_out_role=1"  style="color: #4d4d4d;text-decoration: none;cursor: pointer;font-size: 14px; padding: 0px 100px;" >你有一个新订单</a>
                                <a href="<?php echo url('admin/order/orderedit'); ?>?id=<?php echo $vo['id']; ?>&is_view=<?php echo $upload_view; ?>&is_out_role=1"  style="color: #4d4d4d;text-decoration: none;cursor: pointer;font-size: 14px;    padding: 0px 100px;"><?php echo $vo['workname']; ?></a>
                     

                            </span>
                            

                        
                             <em class="fr" style="font-style: normal;color: #ccc;float: right !important;font-size:12px;"><?php echo $vo['addtime']; ?></em>
                         </p>

                     </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>  
                    
                 </ul>
                <h2>留言消息<h2>
                    <ul>
                    
                    <?php if(is_array($viewopen['message']) || $viewopen['message'] instanceof \think\Collection || $viewopen['message'] instanceof \think\Paginator): $i = 0; $__LIST__ = $viewopen['message'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?>
                    <li  id="li_0" class="msg-read" style="position: relative;padding: 16px 0;border-bottom: 1px solid #e0e0e0;list-style: none;">
                            
                        <p  class="msg-text clearfix" style="display: block;margin-top: 8px;padding: 0;color: #4d4d4d;">
                            <span  class="bb-span-wrap" style="width: 80%;font-weight: 700;text-overflow: ellipsis;white-space: nowrap;overflow: hidden;">
                                <a href="<?php echo url('admin/order/orderedit'); ?>?id=<?php echo $vo['order_id']; ?>&mess_id=<?php echo $vo['mess_id']; ?>&is_message=1"  style="color: #4d4d4d;text-decoration: none;cursor: pointer;font-size: 14px;    padding: 0px 100px;">你有一条新留言</a>
                                <a href="<?php echo url('admin/order/orderedit'); ?>?id=<?php echo $vo['order_id']; ?>&mess_id=<?php echo $vo['mess_id']; ?>&is_message=1"  style="color: #4d4d4d;text-decoration: none;cursor: pointer;font-size: 14px;    padding: 0px 100px;"><?php echo $vo['mess_list']; ?></a>
                              
                            
                            </span>
                             <em class="fr" style="font-style: normal;color: #ccc;float: right !important;font-size:12px;"><?php echo $vo['add_time']; ?></em>
                         </p>

                     </li>
                    <?php endforeach; endif; else: echo "" ;endif; ?>  

                 </ul>
                         
                     <!-- <button class="btn btn-primary" type="submit" style="margin-left: 80px;"><a href="<?php echo url('index/index'); ?>" style="color:white;">返回</a></button>     -->
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
<script src="__JS__/plugins/layer/laydate/laydate.js"></script>
<script src="__JS__/plugins/layer/layer.min.js"></script>
<script type="text/javascript">
         var index = '';



</script>
</body>
</html>
