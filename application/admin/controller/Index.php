<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\NodeModel;
use app\admin\model\NoticeModel;
use app\admin\model\OrderModel;
use app\admin\model\UserModel;
use app\admin\model\GroupModel;
use think\Session;
use think\Db;
use think\Request;

class Index extends Base
{
    public function index()
    {

        // 获取权限菜单
        $node = new NodeModel();

        $a = $node->getMenu(session('rule'));

        $userid = Session::get('user_id');

        $user = new UserModel();

        $flag = $user->getOneUser($userid);

        $wheremessage = [];

        $where = [];

        if ($flag['role_id'] == 3) {

            $order_id = Db::name('order')->where('user_id', $flag['user_id'])->field('id')->select();

            $order_id_sn = [];
            foreach ($order_id as $key => $value) {

                $order_id_sn[$key] = $value['id'];
            }

            $order_id_list = implode(',', $order_id_sn);

            //业务员确认消息通知
            $where['user_id'] = $flag['user_id'];

            $where['is_view'] = '5';

            $where['static'] = '5';

            $wheremessage['order_id'] = array('in', $order_id_list);

        }

        if ($flag['role_id'] == 4) {

            $whereis['role_id'] = $flag['role_id'];
            $whereis['user_id'] = $flag['user_id'];
            $usergroup = $user->getUserfind($whereis);

            $group = new GroupModel();

            // var_dump($usergroup['group_id']);exit;

            $user = $group->getgrouppeople($usergroup['group_id']);

            // 将查到的多个userid组合成字符串

            $a = array();

            foreach ($user as $key => $v) {
                $a[$key] = $v['user_id'];

            }

            $userid = implode(',', $a);

            $order_id = Db::name('order')->where('user_id', 'in', $userid)->field('id')->select();

            $order_id_sn = [];
            foreach ($order_id as $key => $value) {

                $order_id_sn[$key] = $value['id'];

            }

            $order_id_list = implode(',', $order_id_sn);

            $where['user_id'] = array('in', $userid);

            $wheremessage['order_id'] = array('in', $order_id_list);

            $where['is_view'] = 0;

        }


        if ($flag['role_id'] == 6 || $flag['role_id'] == 8 || $flag['role_id'] == 10) {


            $a = array_null($flag['order_id']);

            // var_dump($a);

            $where['id'] = array('in', $a);

            $wheremessage['order_id'] = array('in', $a);

            $where['is_view'] = 2;


        }

        // 手工
        if ($flag['role_id'] == 7 || $flag['role_id'] == 9 || $flag['role_id'] == 11) {

            $a = array_null($flag['order_id']);


            $where['id'] = array('in', $a);

            $wheremessage['order_id'] = array('in', $a);

            $where['is_view'] = 4;

            // $where['static'] = '4';

        }


        $viewopen['order'] = Db::name('order')->where($where)->count();


        //订单消息留言


        //     if($flag['role_id'] == 3 ){

        //       $viewopen['order'] = 0;
        //}
        // 游离于权限外的角色
        // 部长

        if ($flag['role_id'] == 5) {


            $where['is_minister'] = '0';

            $viewopen['order'] = Db::name('order')->where($where)->count();

        }

        // 总经理
        if ($flag['role_id'] == 16) {


            $where['is_boss'] = '0';

            $viewopen['order'] = Db::name('order')->where($where)->count();


        }

        //财务
        if ($flag['role_id'] == 13) {


            $where['is_finance'] = '0';

            $viewopen['order'] = Db::name('order')->where($where)->count();


        }


        //订单消息留言

        // ----------------
        // 留言消息通知
        // ---------------

        $message_zt = array();
        // 业务员
        if ($flag['role_id'] == '3') {

            $wheremessage['order_id'] = array('in', $order_id_list);

            $wheremessage['is_view_message'] = 0;
        }
        // 组长
        if ($flag['role_id'] == '4') {

            $wheremessage['order_id'] = array('in', $order_id_list);

            $wheremessage['is_minister'] = 0;

        }
        // 部长
        if ($flag['role_id'] == '5') {

            $wheremessage['is_bz'] = 0;

        }
        // sla编程
        if ($flag['role_id'] == '6') {

            $a = array_null($flag['order_id']);

            $wheremessage['order_id'] = array('in', $a);

            $wheremessage['is_3d_printing'] = 0;
        }
        // 后处理
        if ($flag['role_id'] == '7') {

            $a = array_null($flag['order_id']);

            $wheremessage['order_id'] = array('in', $a);

            $wheremessage['is_3d_sg'] = 0;
        }
        // cnc
        if ($flag['role_id'] == '8') {

            $a = array_null($flag['order_id']);

            $wheremessage['order_id'] = array('in', $a);

            $wheremessage['is_cnc'] = 0;
        }
        // cnc手工
        if ($flag['role_id'] == '9') {

            $a = array_null($flag['order_id']);

            $wheremessage['order_id'] = array('in', $a);

            $wheremessage['is_cnc_sg'] = 0;
        }
        // 复模
        if ($flag['role_id'] == '10') {

            $a = array_null($flag['order_id']);

            $wheremessage['order_id'] = array('in', $a);

            $wheremessage['is_fumo'] = 0;
        }
        // 复模手工
        if ($flag['role_id'] == '11') {

            $a = array_null($flag['order_id']);

            $wheremessage['order_id'] = array('in', $a);

            $wheremessage['is_fumo_sg'] = 0;
        }

        // 财务
        if ($flag['role_id'] == '13') {

            $wheremessage['is_finance'] = 0;

        }
        // 总经理
        if ($flag['role_id'] == '16') {

            $wheremessage['is_boss'] = 0;

        }

        $viewopen['message'] = Db::name('message')->where($wheremessage)->count();

        $viewopen['add'] = $viewopen['order'] + $viewopen['message'];

        //$request = Request::instance();
        if (Request::instance()->isAjax()) {
            $sum = $viewopen['add'] - 1;
            return $sum;
        }
        $this->assign('viewopen', $viewopen);

        $this->assign([
            'menu' => $node->getMenu(session('rule'))
        ]);

        // 未读消息
        $notice = new NoticeModel();
        $countInfo = $notice -> where('user_id', $flag['user_id'])
                    -> where('is_have_read', 0)
                    -> count();

        $this -> assign('countInfo', $countInfo);

        return $this->fetch('/index');
    }

    /**
     * 后台默认首页
     * @return mixed
     */
    public function indexPage()
    {


        $order = new OrderModel();

        // 今日订单数

        $addtime = date("Y-m-d");

        $where['addtime'] = ['like', '%' . $addtime . '%'];


        $count['addtime'] = $order->getAllorder($where);

        // 总订单数

        $count['allorder'] = $order->getAllorder('1 = 1');

        // 进行中

        $count['alling'] = $order->getAllorder('static != 5');

        // 结束
        $count['allend'] = $order->getAllorder('static = 5');

        // 异常
        $count['debuff'] = $order->getAllorder('debuff = 2');


        // $line['allorder']  = implode(',',$line['allorder']);

        // $line['allend']  = implode(',',$line['allend']);

        // $line['alling']  = implode(',',$line['alling']);

        // $line['debuff']  = implode(',',$line['debuff']);

        $user = new UserModel();

        $userid = Session::get('user_id');
        //  $this->assign('vo',$flag);
        $flag = $user->getOneUser($userid);


        $this->assign([
            'count' => $count
        ]);

        $this->assign('vo', $flag);


        return $this->fetch('index');
    }


    // 首页订单统计列表
    public function orderlist()
    {


        $order = new OrderModel();
        $month = date('m');

        $year = date('Y');


        //这里主要是构建json数据返回

        //构建图例legend
        $legend = array(0 => "全部订单", 1 => "全部完成", 2 => "进行中", 3 => "异常订单");

        //构建横坐标

        //构建数据内容数组
        $series = array();

        //由于需要{"name":"",type":"","data":""}类型的json值，使用关联数组
        $serie1 = array();
        $serie1["name"] = "全部订单";
        $serie1["type"] = "bar";
        $data = $order->dataorder($month, $year, '1 = 1');


        $serie1["data"] = $data;

        //由于需要{"name":"",type":"","data":""}类型的json值，使用关联数组
        $serie2 = array();
        $serie2["name"] = "全部完成";
        $serie2["type"] = "bar";
        $data = $order->dataorder($month, $year, 'static = 5');
        $serie2["data"] = $data;

        $serie3 = array();
        $serie3["name"] = "进行中";
        $serie3["type"] = "bar";
        $data = $order->dataorder($month, $year, 'static != 5');
        $serie3["data"] = $data;

        $serie4 = array();
        $serie4["name"] = "异常订单";
        $serie4["type"] = "bar";
        $data = $order->dataorder($month, $year, 'debuff = 2');
        $serie4["data"] = $data;

        // var_dump($serie4);

        //由于最终的数组内容是["",""]格式的json，所以使用索引数组
        array_push($series, $serie1);
        array_push($series, $serie2);
        array_push($series, $serie3);
        array_push($series, $serie4);


        // 由于需要{"legend":"value1","xAxis_data":"value2","series":"value3"}的json值，应该使用关联数组
        $result = array();
        $result["legend"] = $legend;
        // $result["xAxis_data"] = $xAxis_data;
        $result["series"] = $series;

        //返回json
        header("Content-Type:application/json");
        echo json_encode($result);


    }
}
