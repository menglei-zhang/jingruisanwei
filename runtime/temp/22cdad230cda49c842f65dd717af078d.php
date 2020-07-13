<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:83:"/Applications/MAMP/htdocs/www/jingruisanwei/application/admin/view/index/index.html";i:1590733872;}*/ ?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>后台首页</title>
    <link rel="shortcut icon" href="favicon.ico">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.staticfile.org/font-awesome/4.4.0/css/font-awesome.css?v=4.4.0" rel="stylesheet">
    <link href="https://cdn.bootcss.com/bootstrap/3.3.6/css/bootstrap.min.css" rel="stylesheet">
    <link href="__CSS__/style.min.css?v=4.1.0" rel="stylesheet">
    <link href='__CSS__/tiltedpage-scroll.css' rel='stylesheet' type='text/css'>    
      <style>

    html {

      height: 100%;

    }

    body {

      background: #F1f1f2;

      padding: 0;

      text-align: center;

      font-family: 'open sans';

      position: relative;

      margin: 0;

      height: 100%;

    }

    

    .wrapper {

        height: auto !important;

        height: 100%;

        margin: 0 auto; 

        overflow: hidden;

    }

    

    a {

      text-decoration: none;

    }

    

    

    

    h1, h2 {

      width: 100%;

      float: left;

    }


    .pointer {

      color: #00B0FF;

      font-family: 'Pacifico';

      font-size: 24px;

      margin-top: 15px;

      position: absolute;

      top: 130px;

      right: -40px;

    }

    .pointer2 {

      color: #00B0FF;

      font-family: 'Pacifico';

      font-size: 24px;

      margin-top: 15px;

      position: absolute;

      top: 130px;

      left: -40px;

    }

    pre {

      margin: 80px auto;

    }

    pre code {

      padding: 35px;

      border-radius: 5px;

      font-size: 15px;

      background: rgba(0,0,0,0.1);

      border: rgba(0,0,0,0.05) 5px solid;

      max-width: 500px;

    }





    .main {

      float: left;

      width: 100%;

      margin: 0 auto;

    }

    

    

    .main h1 {

      padding:20px 50px 10px;

      float: left;

      width: 100%;

      font-size: 60px;

      box-sizing: border-box;

      -webkit-box-sizing: border-box;

      -moz-box-sizing: border-box;

      font-weight: 100;

      margin: 0;

      padding-top: 55px;

      font-family: 'Open Sans';

      letter-spacing: -1px;

      text-transform: capitalize;

    }

   

    .main h1.demo1 {

      background: #1ABC9C;

    }

    

    .reload.bell {

      font-size: 12px;

      padding: 20px;

      width: 45px;

      text-align: center;

      height: 47px;

      border-radius: 50px;

      -webkit-border-radius: 50px;

      -moz-border-radius: 50px;

    }

    

    .reload.bell #notification {

      font-size: 25px;

      line-height: 140%;

    }

    

    .reload, .btn{

      display: inline-block;

      border-radius: 3px;

      -moz-border-radius: 3px;

      -webkit-border-radius: 3px;

      display: inline-block;

      line-height: 100%;

      padding: 0.7em;

      text-decoration: none;

      width: 100px;

      line-height: 140%;

      font-size: 17px;

      font-family: Open Sans;

      font-weight: bold;

      -webkit-box-shadow: none;

      box-shadow: none;

      background-color: #4D90FE;

      background-image: -webkit-linear-gradient(top,#4D90FE,#4787ED);

      background-image: -webkit-moz-gradient(top,#4D90FE,#4787ED);

      background-image: linear-gradient(top,#4d90fe,#4787ed);

      border: 1px solid #3079ED;

      color: #FFF;

    }

    .reload:hover{

      background: #317af2;

    }

    .btn {

      width: 200px;

      -webkit-box-shadow: none;

      box-shadow: none;

      background-color: #4D90FE;

      background-image: -webkit-linear-gradient(top,#4D90FE,#4787ED);

      background-image: -moz-linear-gradient(top,#4D90FE,#4787ED);

      background-image: linear-gradient(top,#4d90fe,#4787ed);

      border: 1px solid #3079ED;

      color: #FFF;

    }

    .clear {

      width: auto;

    }

    .btn:hover, .btn:hover {

      background: #317af2;

    }

    .btns {

      float: left;

      width: 100%;

      margin: 50px auto;

    }

    .credit {

      text-align: center;

      color: #888;

      padding: 10px 10px;

      margin: 0 0 0 0;

      background: #f5f5f5;

      float: left;

      width: 100%;

    }

    .credit a {

      text-decoration: none;

      font-weight: bold;

      color: black;

    }

    

    .back {

      position: absolute;

      top: 0;

      left: 0;

      text-align: center;

      display: block;

      padding: 7px;

      width: 100%;

      box-sizing: border-box;

      -moz-box-sizing: border-box;

      -webkit-box-sizing: border-box;

      background:#f5f5f5;

      font-weight: bold;

      font-size: 13px;

      color: #888;

      -webkit-transition: all 200ms ease-out;

      -moz-transition: all 200ms ease-out;

      -o-transition: all 200ms ease-out;

      transition: all 200ms ease-out;

    }

    .back:hover {

      background: #eee;

    }

    

    

    .page-container {

      float: left;

      width: 100%;

      margin: 0 auto 300px;

      position: relative;

    }

    .panorama {

      width: 100%;

      float: left;

      margin-top: -5px;

      height: 700px;

    }

    

    .panorama .credit {

      background: rgba(0,0,0,0.2);

      color: white;

      font-size: 12px;

      text-align: center;

      position: absolute;

      bottom: 0;

      right: 0;

      box-sizing: border-box;

      -webkit-box-sizing: border-box;

      -moz-box-sizing: border-box;

      float: right;

    }

    

    .main {

      margin-bottom: 350px;

      overflow: hidden;

    }

    .tps-section {

      width: 100% !important;

      max-width: 1000px;

      margin: 0 auto;

      height: 500px;

    }

    

    .tps-section .tps-wrapper {

      border-radius: 5px;

    }

    .tps-section .tps-wrapper h1 {

      position: relative;

      height: 100%;

      position: absolute;

    }

    .tps-section .tps-wrapper h1 a{

      color: white;

      position: absolute;

      background: rgba(0,0,0,0.25);

      width: 100%;

      height: 100%;

      top: 0;

      padding-top: 225px;

      box-sizing: border-box;

      -webkit-box-sizing: border-box;

      -moz-box-sizing: border-box;

      left: 0;

      font-weight: bold;

      text-transform: uppercase;

      letter-spacing: 4px;

      font-size: 20px;

      font-size: 14px;

      line-height: 190%;

    }

    

    .tps-section .tps-wrapper h1 a small{

      text-transform: none;

      font-style: italic;

      font-weight: 400;

      font-family: noto serif;

      letter-spacing: 1px;

      font-size: 14px;

    }

    

    .tps-section:nth-child(1n+1) .tps-wrapper {

      background: url('__IMG__/jr1.jpg') center center;

      background-size: cover;

    }

    

    .tps-section:nth-child(2n+1) .tps-wrapper {

      background:  url('__IMG__/jr2.jpg') center center;

      background-size: cover;

    }

    

    .tps-section:nth-child(3n+1) .tps-wrapper {

      background:  url('__IMG__/jr3.jpg') center center;

      background-size: cover;

    }

    

    .tps-section:nth-child(4n+1) .tps-wrapper {

      background:  url('__IMG__/jr4.jpg') center center;

      background-size: cover;

    }

    

    .header {

      overflow: hidden;

      clear: both;

    }

    </style>



</head>
<body class="gray-bg">


 

<!-- <?php if($vo['role_id'] != '5' &&  $vo['role_id'] != '16'): ?>
 -->


 <div class="wrapper"> 

        

      <div class="main">

        <div class="header">

        <h1 style="margin-top: 5px;color: #999;margin-bottom: 5px;font-size: 70px;font-weight: 100;">欢迎使用</h1>

        <h2 style="padding: 00px 20px 30px 20px;box-sizing: border-box;-webkit-box-sizing: border-box;-moz-box-sizing: border-box;letter-spacing: 0px;color: #888;font-size: 20px;line-height: 160%;font-weight: 100;margin-top: 10px;margin-bottom: 0;" >精锐三维科技</h2>

       

        

      </div>

      <section class="page1">

        <div class="page_container">

          <h1><a href="#" >Travel to Sydney, Australia<br><small>Completed on 14 Feb 2014</small></a></h1>

        </div>

      </section>

      <section class="page2">

        <div class="page_container">

          <h1><a href="#" >Visit Uluru (Ayers Rock), Australia<br><small>Completed on 17 Feb 2014</small></a></h1>

        </div>

      </section>

      <section class="page3">

        <div class="page_container">

          <h1><a href="" >Travel to ZhangJiaJie (Avatar's Mountain) in China<br><small>Completed on 20 Jan 2014</small></a></h1>

        </div>

      </section>

      <section class="page4">

        <div class="page_container">

          <h1><a href="#" >Ride a Camel in the Australian Outback<br><small>Completed on 17 Feb 2014</small></a></h1>

        </div>

      </section>


    </div>

    

  </div>


<!-- <?php else: ?>     -->

<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">今天</span>
                    <h5>今日订单数</h5>
                </div>
                <div class="ibox-content" style="padding: 15px 20px 45px;">
                    <h1 class="no-margins"><?php echo $count['addtime']; ?></h1>
                    <div class="stat-percent font-bold text-navy"></div>
                    <small></small>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title" >
                    <span class="label label-primary pull-right">今天</span>
                    <h5>总订单数</h5>
                </div>
                <div class="ibox-content" style="padding: 15px 20px 45px;">
                    <h1 class="no-margins"><?php echo $count['allorder']; ?></h1>
                    <div class="stat-percent font-bold text-danger"></div>
                    <small></small>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">今天</span>
                    <h5>正在进行中的订单</h5>
                </div>
                <div class="ibox-content" style="padding: 15px 20px 45px;">
                    <h1 class="no-margins"><?php echo $count['alling']; ?></h1>
                    <div class="stat-percent font-bold text-danger"></div>
                    <small></small>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">今天</span>
                    <h5>已完成的订单</h5>
                </div>
                <div class="ibox-content" style="padding: 15px 20px 45px;">
                    <h1 class="no-margins"><?php echo $count['allend']; ?></h1>
                    <div class="stat-percent font-bold text-danger"></div>
                    <small></small>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">今天</span>
                    <h5>异常订单</h5>
                </div>
                <div class="ibox-content" style="padding: 15px 20px 45px;">
                    <h1 class="no-margins"><?php echo $count['debuff']; ?></h1>
                    <div class="stat-percent font-bold text-danger"></div>
                    <small></small>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-8">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>当月数据分析</h5>
                </div>
                <div class="ibox-content no-padding">
                    <div class="ibox-content" style="height: 350px" id="main">

                    </div>
                </div>
            </div>
        </div>
        

    </div>
</div>
<!-- <?php endif; ?> -->

<script src="https://cdn.staticfile.org/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="__JS__/plugins/echarts/echarts-all.js"></script>
 <script type="text/javascript" src="__JS__/jquery.tiltedpage-scroll.min.js"></script>
 <script>

      $(document).ready(function(){

      $(".main").tiltedpage_scroll({

        angle: 20

      });

        });

        

  </script>
<script type="text/javascript">

    
      var yData = [];  

      $.ajax({
          url:"<?php echo url('index/orderlist'); ?>",
          type:'post',
          dataType:"json",
          success:function(data){

             getChart(data)
          }
      })
      
     
    function  getChart (data){

      // console.log(data);
      // 基于准备好的dom，初始化echarts实例
      var myChart = echarts.init(document.getElementById('main'));

      // 指定图表的配置项和数据
     option = {
      tooltip : {
          trigger: 'axis',
          axisPointer : {            // 坐标轴指示器，坐标轴触发有效
              type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
          }
      },
      legend: {
          data:data.legend
      },
      toolbox: {
          show : true,
          orient: 'vertical',
          x: 'right',
          y: 'center',
          feature : {
              mark : {show: true},
              dataView : {show: true, readOnly: false},
              magicType : {show: true, type: ['line', 'bar', 'stack', 'tiled']},
              restore : {show: true},
              saveAsImage : {show: true}
          }
      },
      calculable : true,
      xAxis : [
          {
              type : 'category',
              data: ['1','2','3','4','5','6','7','8','9','10','11','12','13','14','15','16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31']
          }
      ],
      yAxis : [
          {
              type : 'value'
          }
      ],
     series : data.series
  };

      // 使用刚指定的配置项和数据显示图表。
      myChart.setOption(option);

    }


</script>
</body>
</html>
