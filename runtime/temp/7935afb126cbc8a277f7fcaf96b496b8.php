<?php if (!defined('THINK_PATH')) exit(); /*a:1:{s:59:"D:\wamp64\www\snake/application/admin\view\index\index.html";i:1557282540;}*/ ?>
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
</head>
<body class="gray-bg">
<div class="wrapper wrapper-content">
    <div class="row">
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">今天</span>
                    <h5>今日订单数</h5>
                </div>
                <div class="ibox-content">
                    <h1 class="no-margins"><?php echo $count['addtime']; ?></h1>
                    <div class="stat-percent font-bold text-navy"></div>
                    <small></small>
                </div>
            </div>
        </div>
        <div class="col-sm-2">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <span class="label label-primary pull-right">今天</span>
                    <h5>总订单数</h5>
                </div>
                <div class="ibox-content">
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
                <div class="ibox-content">
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
                <div class="ibox-content">
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
                <div class="ibox-content">
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
        


<!--         <div class="col-sm-4">
            <div class="ibox float-e-margins">
                <div class="ibox-title">
                    <h5>系统说明</h5>
                </div>
                <div class="ibox-content">
                    <p>snake 一个采用thinkphp5开发，
                        旨在为大家提供一个可用的便捷的后台系统。
                    </p>
                    <div class="alert alert-info">
                        交流群： 159640205
                    </div>
                </div>
            </div>
        </div> -->
    </div>
</div>

<script src="https://cdn.staticfile.org/jquery/2.1.4/jquery.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
<script src="__JS__/plugins/echarts/echarts-all.js"></script>
<script type="text/javascript">
    var data = <?php echo $show_data; ?>;

     console.log(data);

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
        data:['所有订单','完成订单','进行中','异常订单']
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
    series : [
        {
            name:'所有订单',
            type:'bar',
            data:data.allorder
        },

        {
            name:'完成订单',
            type:'bar',
            data:data.allend
        },
         {
            name:'进行中',
            type:'bar',
            data:data.alling
        },
         {
            name:'异常订单',
            type:'bar',
            data:data.debuff
        }
    ]
};

    // 使用刚指定的配置项和数据显示图表。
    myChart.setOption(option);
</script>
</body>
</html>
