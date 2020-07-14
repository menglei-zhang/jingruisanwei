<?php


namespace app\api\controller;

use app\admin\model\NoticeModel;

use think\Cache;

use think\Db;

use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use app\admin\model\CustomerModel;

use think\Request;

use think\Session;

use app\index\controller\Message;

use app\api\controller\Qywx;

class Orders extends Base
{
    /**
     * 订单列表
     * user_id    用户ID
     * role_id   权限ID
     */
    public function orderList()
    {

        if ($this->request->isPost()) {


            $code = config('code');

            $msg = config('msg');

            $data = input('post.');

            if (empty($data['user_id'])) {

                $error = array('code' => $code['ParamEmpty'], 'msg' => 'user_id为空');

                return json($error);

                exit;
            }


            $datas = Db::name('user')->where('user_id', $data['user_id'])->find();


            $where = [];


            $user = new UserModel();


            if ($datas['role_id'] == 3 || $datas['role_id'] == 19) {

                $where['user_id'] = $data['user_id'];
            }

            // 销售部组长  同上
            if ($datas['role_id'] == 4) {

                $whereis['role_id'] = $datas['role_id'];
                $whereis['user_id'] = $data['user_id'];
                $usergroup = $user->getUserfind($whereis);


                $group = new GroupModel();

                // var_dump($usergroup['group_id']);exit;

                $user = $group->getgrouppeople($usergroup['group_id']);


                // 将查到的多个userid组合成字符串

                $a = array();

                foreach ($user as $key => $v) {
                    $a[$key] = $v['user_id'];

                }
                array_unshift($a, $data['user_id']);

                $userid = implode(',', $a);

                $where['user_id'] = array('in', $userid);

            }

            // 销售部部长
            if ($datas['role_id'] == 5) {

                $group = new GroupModel();

                $allgroup = $group->getGroupList();

                $a = getTree(objToArray($allgroup));

                // var_dump($a);exit;

                foreach ($a[0]['children'] as $k => $v) {

                    $b[] = $v['id'];

                    foreach ($v['children'] as $key => $value) {


                        array_push($b, $value['id']);


                    }

                }


                $groupid = implode(',', $b);

                $whereis['group_id'] = array('in', $groupid);

                $usergroup = $user->getUser($whereis);

                foreach ($usergroup as $kl => $val) {

                    $c[$kl] = $val['user_id'];

                }

                $userid = implode(',', $c);


                $where['user_id'] = array('in', $userid);


            }


            // 编程

            if ($datas['role_id'] == 6 || $datas['role_id'] == 8 || $datas['role_id'] == 10) {


                $order_id = explode(',', $datas['order_id']);


                array_filter($order_id);

                $where['id'] = array('in', $order_id);


            }

            // 手工
            if ($datas['role_id'] == 7 || $datas['role_id'] == 9 || $datas['role_id'] == 11) {


                $order_id = explode(',', $datas['order_id']);


                array_filter($order_id);


                $where['id'] = array('in', $order_id);

                // $where['static'] = '4';


            }

            $order = array();

            $order = Db::name('order')->where($where)->order('id desc')->select();

            if (empty($order)) {

                $error = array('data' => $order, 'code' => $code['RequestSuccess'], 'msg' => '该用户订单为空');

                return json($error);

                exit;
            }

            // var_dump($order);exit;
            foreach ($order as $k => $v) {
                $orders[$k]['id'] = $v['id'];
                $orders[$k]['addTime'] = $v['addtime'];

                // var_dump($orders[$k]['id']);

                @$orders[$k]['username'] = $v['username'];
                @$orders[$k]['order_sn'] = $v['order_sn'];
                @$orders[$k]['workname'] = $v['workname'];

                @$grop_id = Db::name('group')->where('id', $v['confirm'])->find();

                // $orders[$k]['confirm'] = $grop_id['group_name'];

                $static = config('static');

                $debuff = config('debuff');

                @$orders[$k]['confirm'] = $grop_id['group_name'];

                if (isset($static[$v['static']]) && $static[$v['static']]) {
                    $orders[$k]['confirm'] .= "-{$static[$v['static']]}";
                }

                if (isset($debuff[$v['debuff']]) && $debuff[$v['debuff']]) {
                    $orders[$k]['confirm'] .= "-{$debuff[$v['debuff']]}";
                }

                @$orders[$k]['static'] = $v['static'];

                @$orders[$k]['debuff'] = $v['debuff'];

                @$uptime = config('uptime');
                @$updatatime = $uptime[$v['uptime']];
                @$orders[$k]['update'] = $v['update'] . ' ' . $updatatime;
            }

            $list = array('data' => $orders, 'msg' => $msg['RequestSuccess'], 'code' => $code['RequestSuccess']);

            return json($list);

            exit;

        } else {

            return json(echoArr(500, '非法请求'));

        }

    }
    // 订单详情

    // order_id   订单id
    // user_id   用户id
    public function orderDetails()
    {
        if ($this->request->isPost()) {

            $code = config('code');

            $msg = config('msg');

            $data = input('post.');

            if (empty($data['order_id'])) {

                $error = array('code' => $code['ParamEmpty'], 'msg' => '订单id为空');

                return json($error);

                exit;
            }

            if (empty($data['user_id'])) {

                $error = array('code' => $code['ParamEmpty'], 'msg' => '用户ID为空');

                return json($error);

                exit;
            }
            $orderlist = Db::name('order')->where('id', $data['order_id'])->find();

            if ($orderlist == null) {

                $error = array('code' => $code['ParamEmpty'], '查询数据为空或订单ID错误');

                return json($error);

                exit;
            }


            $message = Db::name('message')->where('order_id', $data['order_id'])->select();

            if (!empty($orderlist['pc_src'])) {

                $imgs = explode(',', $orderlist['pc_src']);

                foreach ($imgs as $key => $value) {
                    $temp = pathinfo($value);

                    if(in_array($temp['extension'], ['jpg', 'jpeg', 'png', 'gif'])){
                        $imgs[$key] = request() -> domain() . '/public' . $value;
                    } else {
                        unset($imgs[$key]);
                    }
                }

            } else {

                $imgs = array();
            }

            $orderdetails = array();

            $orderdetails['machine_number'] = '';

            $orderdetails['date'] = '';

            $orderdetails['file_name'] = '';


            if (empty($message)) {
                $static = config('static');
                $debuff = config('debuff');

                $grop_id = Db::name('group')->where('id', $orderlist['confirm'])->find();

                // $orders[$k]['confirm'] = $grop_id['group_name'];

                $static = config('static');

                $debuff = config('debuff');

                @ $orderdetails['static'] = $grop_id['group_name'] . '-' . $static[$orderlist['static']] . '-' . $debuff[$orderlist['debuff']];


                if ($orderlist['static'] == 2) {
                    $orderdetails['staticnum'] = 1;
                } elseif ($orderlist['static'] == 3) {
                    $orderdetails['staticnum'] = 2;
                } else if($orderlist['static'] == 1) {
                    $orderdetails['staticnum'] = 0;
                } else {
                    $orderdetails['staticnum'] = 1;
                }

                $orderdetails['order_id'] = $orderlist['id'];
                @ $orderdetails['pc_src'] = $imgs;
                $orderdetails['username'] = $orderlist['username'];
                $orderdetails['workname'] = $orderlist['workname'];


                $orderdetails['confirm'] = $grop_id['group_name'];

                $orderdetails['confirm_num'] = $orderlist['confirm'];

                $orderdetails['craft'] = $orderlist['craft'];
                $orderdetails['order_sn'] = $orderlist['order_sn'];
                $orderdetails['place'] = $orderlist['place'];

                $uptime = config('uptime');
                $updatatime = $uptime[$orderlist['uptime']];
                $orderdetails['update'] = $orderlist['update'] . ' ' . $updatatime;

                $orderdetails['sanding'] = $orderlist['sanding'];
                // $orderdetails['file_name'] = $orderlist['file_name'];
                $orderdetails['num'] = $orderlist['num'];
                $orderdetails['weight'] = $orderlist['weight'];
                $orderdetails['material'] = $orderlist['material'];
                $orderdetails['machine_number'] = $orderlist['machine_number'];
                $orderdetails['date'] = $orderlist['date'];
                // $orderdetails['programmer'] = $orderlist['programmer'];
                $orderdetails['be_careful'] = $orderlist['be_careful'];

                $orderdetails['message'] = array();
            } else {


                foreach ($message as $k => $v) {

                    $request = Request::instance();

                    $domain = $request->domain();

                    $static = config('static');

                    $debuff = config('debuff');

                    $grop_id = Db::name('group')->where('id', $orderlist['confirm'])->find();

                    // $orders[$k]['confirm'] = $grop_id['group_name'];

                    $static = config('static');

                    $debuff = config('debuff');

                    @ $orderdetails['static'] = $grop_id['group_name'] . '-' . $static[$orderlist['static']] . '-' . $debuff[$orderlist['debuff']];

                    // $orderdetails['static'] = $orderlist['confirm'].'-'.$static[$orderlist['static']].'-'.$debuff[$orderlist['debuff']];

                    if ($orderlist['static'] == 2) {
                        $orderdetails['staticnum'] = 1;
                    } elseif ($orderlist['static'] == 3) {
                        $orderdetails['staticnum'] = 2;
                    } else if($orderlist['static'] == 1) {
                        $orderdetails['staticnum'] = 0;
                    } else {
                        $orderdetails['staticnum'] = 1;
                    }

                    $orderdetails['order_id'] = $orderlist['id'];
                    @ $orderdetails['pc_src'] = $imgs;
                    $orderdetails['username'] = $orderlist['username'];
                    $orderdetails['workname'] = $orderlist['workname'];
                    $orderdetails['confirm'] = $grop_id['group_name'];
                    $orderdetails['confirm_num'] = $orderlist['confirm'];
                    $orderdetails['order_sn'] = $orderlist['order_sn'];
                    $orderdetails['craft'] = $orderlist['craft'];

                    $orderdetails['place'] = $orderlist['place'];

                    $uptime = config('uptime');
                    $updatatime = $uptime[$orderlist['uptime']];
                    $orderdetails['update'] = $orderlist['update'] . ' ' . $updatatime;

                    $orderdetails['sanding'] = $orderlist['sanding'];
                    // $orderdetails['file_name'] = $orderlist['file_name'];
                    $orderdetails['num'] = $orderlist['num'];
                    $orderdetails['weight'] = $orderlist['weight'];
                    $orderdetails['material'] = $orderlist['material'];
                    $orderdetails['machine_number'] = $orderlist['machine_number'];
                    $orderdetails['date'] = $orderlist['date'];
                    // $orderdetails['programmer'] = $orderlist['programmer'];
                    $orderdetails['be_careful'] = $orderlist['be_careful'];


                    if ($v['user_id'] == $data['user_id']) {

                        $orderdetails['message'][$k]['static'] = 1;

                    } else {

                        $orderdetails['message'][$k]['static'] = 0;

                    }

                    $orderdetails['message'][$k]['mess_id'] = $v['mess_id'];
                    $orderdetails['message'][$k]['mess_list'] = $v['mess_list'];
                    $orderdetails['message'][$k]['mess_addtime'] = $v['add_time'];
                    $orderdetails['message'][$k]['real_name'] = $v['real_name'];
                    //$orderdetails['message'][$k]['head'] = $domain.$v['head'];
                    $orderdetails['message'][$k]['head'] = strstr($v['head'], 'http') === false ? $domain.$v['head'] : $v['head'];
                    $mess_img = explode(',', $v['mess_img']);
                    foreach ($mess_img as $n => $va) {
                        if (!$va == null)
                            $mess_img[$n] = $va;
                        else
                            $mess_img = array();

                    }

                    $orderdetails['message'][$k]['mess_img'] = $mess_img;

                }


            }


            $list = array('data' => $orderdetails, 'msg' => $msg['RequestSuccess'], 'code' => $code['RequestSuccess']);


            return json($list);


        } else {

            return json(echoArr(500, '非法请求'));

        }

    }

    // 添加订单留言
    // mess_list  留言
    // mess_img  评论图片
    // user_id 用户ID
    // order_id  订单ID
    public function orderMessageAdd()
    {

        $code = config('code');

        $msg = config('msg');

        if ($this->request->isPost()) {


            $data['mess_list'] = '';

            $data = input('post.');

            if (empty($data['user_id'])) {

                $error = array('code' => $code['ParamEmpty'], 'msg' => '用户id为空');

                return $error;

                exit;
            }
            if (empty($data['order_id'])) {

                $error = array('code' => $code['ParamEmpty'], 'msg' => '订单ID为空');

                return $error;

                exit;
            }

            if (empty($data['mess_img'])) {

                $data['mess_img'] = array();
            }


            if (count($data['mess_img']) > 3) {

                $error = array('code' => $code['RequestFail'], 'msg' => '图片不能超过3张');

                return json($error);

                exit;

            }

            $userlist = Db::name('user')->where('user_id', $data['user_id'])->find();

            $request = Request::instance();

            $domain = $request->domain();

            $data['head'] = $domain . $userlist['head'];

            $data['real_name'] = $userlist['real_name'];

            $data['add_time'] = date("Y-m-d h:i:s");

            // var_dump($data);exit;

            $result = db('message')->insert($data);

            // 消息通知
            $this->messageNotice($data['user_id'], $data['order_id']);

            $list = array('msg' => $msg['RequestSuccess'], 'code' => $code['RequestSuccess']);

            return json($list);


        } else {

            return json(echoArr(500, '非法请求'));

        }

    }

    /**
     * 留言通知
     *
     * @param $userId       用户id
     * @param $orderId      订单id
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function messageNotice($userId, $orderId)
    {
        $orderInfo = Db::table('snake_order')->where('id', $orderId)->find();

        $message = [
            '订单有新的留言,请及时查看!',
            $orderInfo['order_sn'],
            $orderInfo['username'],
            $orderInfo['workname'],
            '',
            ''
        ];

        $userInfo = Db::table('snake_user') -> where('user_id', $userId) -> find();

        // 获取 财务 生产部(3d打印/CNC/复模) 手工(3d打印/CNC/复模) 业务员id
        $userList = Db::table('snake_user')->where('order_id', 'like', "%{$orderId}%")
            ->whereOr('role_id', 13)
            ->whereOr('user_id', $orderInfo['user_id'])
            ->field('user_id,no_open_id,group_id')
            ->select();

        // 获取组长id
        $groupId = 0;
        foreach ($userList as $v) {
            if ($v['user_id'] == $orderInfo['user_id']) $groupId = $v['group_id'];
        }
        $teamGroupId = Db::table('snake_group')->where('id', $groupId)->value('type_id');
        $teamInfo = Db::table('snake_user')->where('group_id', $teamGroupId)
            ->field('user_id,no_open_id,group_id')
            ->find();
        array_push($userList, $teamInfo);

        // 发送消息给参与此订单的用户
        $saveData = [];
        foreach ($userList as $v) {
            // 过滤发留言的用户
            if ($userId != $v['user_id']) {
                if ($v['no_open_id']) Message::send($v['no_open_id'], $message);

                // 业务通知：业务员
                $saveData[] = [
                    'notice_id' => $orderId,
                    'content' => "{$userInfo['user_name']}发布了新留言，请查看",
                    'user_id' => $v['user_id'],
                    'send_user_id' => $userId,
                    'type' => 1,
                    'add_time' => time(),
                    'order_id' => $orderId
                ];
            }
        }

        $notice = new NoticeModel();
        $notice -> isUpdate(false) -> saveAll($saveData);
    }

    // 删除留言
    // user_id
    // mess_id
    public function delmessage()

    {

        if (request()->isPost()) {

            $code = config('code');

            $msg = config('msg');

            $param = input('post.');

            // var_dump($param['user_id']);
            if (empty($param['user_id'])) {


                $error = array('code' => $code['ParamEmpty'], 'msg' => 'userid为空');

                return json($error);

                exit;
            }

            if (empty($param['mess_id'])) {

                $error = array('code' => $code['ParamEmpty'], 'msg' => 'mess_id为空');

                return json($error);

                exit;


            }

            $grop_id = Db::name('message')->where('mess_id', $param['mess_id'])->field('user_id')->find();


            if ($param['user_id'] == $grop_id['user_id']) {

                $result = db('message')->where('mess_id', $param['mess_id'])->delete();


                $list = array('msg' => $msg['RequestSuccess'], 'code' => $code['RequestSuccess']);

                return json($list);


            } else {
                $error = array('code' => $code['RequestFail'], 'msg' => '无法删除非本用户的留言');

                return json($error);

                exit;
            }

        } else {


            return json(echoArr(500, '非法请求'));

        }

    }

    /**
     * 订单数据处理
     *
     * @param $roleId           权限 Id
     * @param $orderId          订单 Id
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function orderHandler($roleId, $orderId)
    {
        $user = new UserModel();

        $groupId = Db::name('group')->where('role_id', $roleId)->value('id');
        $userList = $user->where('group_id', $groupId)->order('user_id asc')->select();

        if (!$userList) return echoArr(1006, '请先添加当前部门的处理人员');

        // 处理生产部接订单的用户
        list($key, $tempData) = [0, []];
        if (1 == count($userList)) {
            $tempData = $userList[$key]['order_id'] ? explode(',', $userList[$key]['order_id']) : [];
        } else {
            // 拿到订单最少的生产人员
            foreach ($userList as $k => $v) {
                $temp = $v['order_id'] ? explode(',', $v['order_id']) : [];

                // 默认第一个为最少的订单数
                if (!isset($count)) {
                    $count = count($temp);
                    continue;
                }

                // 快速排序
                if ($count > count($temp)) {
                    $key = $k;
                    $tempData = $temp;

                    $count = count($temp);
                }
            }
        }

        // 已存在，则不再添加
        if (!in_array($orderId, $tempData)) {
            array_unshift($tempData, $orderId);
            $userList[$key]['order_id'] = implode(',', $tempData);
        }

        return echoArr(200, '', $userList[$key]->toArray());
    }
    // 输出验证数据格式
    public function echoArr($code,$msg,$data = []){
        return [
            'code' => $code,
            'msg' => $msg,
            'data' => $data
        ];
    }
  	// user_id
  	public function getRoleId(){
    	if($this->request->isPost()){
        	$data = input('post.');
          	$data['role_id'] = Db::name('user')->where('user_id',$data['user_id'])->value('role_id');
          	return json(echoArr(200,'请求成功',$data));
        }
    }
    // 确定订单
    // order_id
    // role_id
    // confirm
  	// userId
    public function orderConfirm()
    {
        $content = new OrderModel();
        $user = new UserModel();
        $code = config('code');
        $msg = config('msg');
        if ($this->request->isPost()) {
            $data = input('post.');
            if (empty($data['order_id'])) {
                $error = array('code' => $code['ParamEmpty'], 'msg' => '订单id为空');
                return $error;
                exit;
            }
            if (empty($data['role_id'])) {
                $error = array('code' => $code['ParamEmpty'], 'msg' => '权限id为空');
                return $error;
                exit;
            }
            if (empty($data['confirm'])) {
                $error = array('code' => $code['ParamEmpty'], 'msg' => '生产部值为空');
                return $error;
                exit;
            }
            $orderstatic = Db::name('order')->where('id', $data['order_id'])->find();
            $user_ids = $orderstatic['user_id'];
            $QYWXUserIds = Db::name('user')->where('user_id',$user_ids)->value('qywx_id');
            $data = array_merge($orderstatic, $data);
            // 组长审核
            if ($data['role_id'] == '4') {
                if ($orderstatic['static'] != 1) {
                    $error = array('code' => 201, 'msg' => '订单已提交');
                    return json($error);
                    exit;
                }
                $Qywx = new Qywx();
                // 组长提交，通过审核
                $user = new UserModel();
          		$data['user_name'] = $user->where('user_id',$data['userId'])->value('user_name');
                if(!$data['type']){
                    switch ($data['confirm']) {
                        // 3d 打印
                        case 60:
                            $roleId = 6;
                            break;
                        // CNC
                        case 63:
                            $roleId = 8;
                            break;
                        // 复模
                        case 66:
                            $roleId = 10;
                            break;
                    }
                    $result = $this->orderHandler($roleId, $data['order_id']);
                    if (200 != $result['code']) return json($result);
                    $editUser = $result['data'];
                } else {
                    $editUser = $user -> find($orderstatic['designation_user']) -> toArray();
                    $temp = explode(',', $editUser['order_id']);
                    $temp[] = $orderstatic['id'];
                    $editUser['order_id'] = implode(',', $temp);
                }
                // 修改订单进程状态和消息提示状态.
                Db::name('order')->where('id', $data['order_id'])->update(['static' => '2']);
                $user->isUpdate(true)->save($editUser, ['user_id' => $editUser['user_id']]);

                $message = [
                    '有新的订单已经生成,请及时查看!',
                    $data['order_sn'],
                    $data['username'],
                    $data['workname'],
                    '',
                    ''
                ];
                // 组长：通知 3D打印 / 复模 / CNC
                if ($editUser['no_open_id']) Message::send($editUser['no_open_id'], $message);
                // 业务通知
                $userInfo = $user -> find(Session::get('user_id'));
                // 组长：通知财务部
                $saveData = [];
                $finance = $user->where('role_id', 13)->select();
                if ($finance) {
                    foreach ($finance as $v) {
                        $saveData[] = [
                            'notice_id' => $data['order_id'],
                            'content' => $data['user_name'] . '已审核此订单，请查看',
                            'user_id' => $v['user_id'],
                            'send_user_id' => $data['userId'],
                            'add_time' => time()
                        ];
                        if ($v['no_open_id']) Message::send($v['no_open_id'], $message);
                        // 企业微信通知
                        $QYWXUserId = Db::name('user')->where('user_id',$v['user_id'])->value('qywx_id');
                        if($QYWXUserId){
                            $Qywx->sendQYWXMessage($QYWXUserId,$data['user_name'] . '%%财务%%已审核此订单，请查看');
                        }
                    }
                }

                // 通知 3D打印 / 复模 / CNC
                array_push($saveData, [
                    'notice_id' => $data['order_id'],
                    'content' => $data['user_name'] . '已审核此订单，请处理',
                    'user_id' => $editUser['user_id'],
                    'send_user_id' => $data['userId'],
                    'add_time' => time()
                ]);
                // 企业微信通知
                $QYWXUserId = Db::name('user')->where('user_id',$editUser['user_id'])->value('qywx_id');
                if($QYWXUserId){
                    $Qywx->sendQYWXMessage($QYWXUserId,$data['user_name'] . '%%生产%%已审核此订单，请处理');
                }
                // 通知总经理
                $adminInfo = $user -> where('role_id', 16) -> select();
                foreach($adminInfo as $v){
                    $saveData[] = [
                        'notice_id' => $data['order_id'],
                        'content' => $data['user_name'] . '已审核此订单，请查看',
                        'user_id' => $v['user_id'],
                        'send_user_id' => $data['userId'],
                        'add_time' => time()
                    ];
                    // 企业微信通知
                    $QYWXUserId = Db::name('user')->where('user_id',$v['user_id'])->value('qywx_id');
                    if($QYWXUserId){
                        $Qywx->sendQYWXMessage($QYWXUserId,$data['user_name'] . '%%总经理%%已审核此订单，请查看');
                    }
                }
                // 通知业务员
                if($QYWXUserIds){
                    $Qywx->sendQYWXMessage($QYWXUserIds,$data['user_name'] . '%%业务员%%已审核此订单，请查看');
                }

                $notice = new NoticeModel();
              	return json(echoArr(200,'提交成功'));
            }
            // 生产部编辑提交给手工
            if ($data['role_id'] == '6' || $data['role_id'] == '8' || $data['role_id'] == '10') {
                if ($data['static'] != 2) {
                    $error = array('code' => 201, 'msg' => '订单已提交');
                    return json($error);
                }
                switch ($data['confirm']) {
                    // 3d 打印
                    case 60:
                        $roleId = 7;
                        break;
                    // CNC
                    case 63:
                        $roleId = 9;
                        break;
                    // 复模
                    case 66:
                        $roleId = 11;
                        break;
                }
                $result = $this->orderHandler($roleId, $data['order_id']);
                if (200 != $result['code']) return json($result);

                $editUser = $result['data'];

                // 生产部门：通知 3D打印 手工 / 复模 手工 / CNC 手工
                $message = [
                    '有新的订单已经生成,请及时查看!',
                    $orderstatic['order_sn'],
                    $orderstatic['username'],
                    $orderstatic['workname'],
                    '',
                    ''
                ];
                if ($editUser['no_open_id']) Message::send($editUser['no_open_id'], $message);

                // 业务通知
          		$data['user_name'] = Db::name('user') ->where('user_id',$data['userId'])->value('user_name');
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $data['order_id'],
                    'content' => $data['user_name'] . '已确认过此订单，请处理',
                    'user_id' => $editUser['user_id'],
                    'send_user_id' => $data['userId'],
                    'add_time' => time()
                ]);
                // 修改订单进程状态和消息提示状态
                // 企业微信通知
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$editUser['user_id'])->value('qywx_id');
                    if($QYWXUserId){
                    $Qywx->sendQYWXMessage($QYWXUserId,$data['user_name'] . '%%手工%%已确认过此订单，请处理');
                }
                if($QYWXUserIds){
                    $Qywx->sendQYWXMessage($QYWXUserIds,$data['user_name'] . '%%业务员%%已确认过此订单，请查看');
                }
                $res = Db::name('order')->where('id', $data['order_id'])->update(['static' => '4']);
                $user->isUpdate(true)->save($editUser, ['user_id' => $editUser['user_id']]);
              	return json(echoArr(200,'提交成功'));
            }
            // 3D打印 手工 / 复模 手工 / CNC 手工
            if ($data['role_id'] == '7' || $data['role_id'] == '9' || $data['role_id'] == '11') {
                if ($data['static'] != 4) {
                    $error = array('code' => 201, 'msg' => '订单已提交');
                    return json($error);
                    exit;
                }
                // 手工：消息 通知到 业务员 审核
                $openId = $user -> where('user_id', $user_ids) -> value('no_open_id');
                $message = [
                    '有新的订单已经生成,请及时查看!',
                    $orderstatic['order_sn'],
                    $orderstatic['username'],
                    $orderstatic['workname'],
                    '',
                    ''
                ];
                if ($openId) Message::send($openId, $message);
                // 业务通知
                $notice = new NoticeModel();
              	$res = Db::name('user')->where('user_id',$data['userId'])->find();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $data['order_id'],
                    'content' => $res['user_name'] . '已处理完成此订单，请核实',
                    'user_id' => $data['userId'],
                    'send_user_id' => $res['user_id'],
                    'add_time' => time()
                ]);
                
                // 修改订单进程状态和消息提示状态.
                $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '5']);
                // 企业微信通知
                $Qywx = new Qywx();
                if($QYWXUserIds){
                    $Qywx->sendQYWXMessage($QYWXUserIds,$res['user_name'] . '%%业务员%%已处理完成此订单，请核实');
                }
              	return json(echoArr(200,'提交成功'));
            }
            // 业务员：审核失败后再提交
            if ($data['role_id'] == 3) {
              	if($data['static'] == 3){
                  // 通知组长
                  $userInfo = Db::name('user')->where('user_id', $data['user_id'])->find();
                  $message = [
                      '有新的订单等待您审核!',
                      $data['order_sn'],
                      $data['username'],
                      $data['workname'],
                      '',
                      ''
                  ];
                  $this->teamLeader($userInfo, $message);

                  // 业务通知
                  $this -> teamLeaderNotice($userInfo, $data['id'], '%%组长%%再次提交了订单');
                  // 修改订单进程状态和消息提示状态.
                  $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '1']);
                  return json(echoArr(200,'提交成功'));
                }
                if($data['static'] == 5){
					$flag = $content->editorder($data);
                    // 业务员：审核通过通知手工
                    switch ($data['confirm']) {
                        // 3d 打印
                        case 60:
                            $roleId = 7;
                            break;
                        // CNC
                        case 63:
                            $roleId = 9;
                            break;
                        // 复模
                        case 66:
                            $roleId = 11;
                            break;
                    }
                    $groupId = Db::name('group')->where('role_id', $roleId)->value('id');
                    $userInfo = $user->where('group_id', $groupId)
                        ->where('order_id', 'like', "%{$data['id']}%")
                        ->find();
                    $message = [
                        '您的订单已通过,请及时查看!',
                        $data['order_sn'],
                        $data['username'],
                        $data['workname'],
                        '',
                        ''
                    ];
                    if($userInfo) Message::send($userInfo['no_open_id'], $message);
                    // 业务通知
              		$res = Db::name('user')->where('user_id',$data['userId'])->find();
                    $notice = new NoticeModel();
                    $notice -> isUpdate(false) -> save([
                        'notice_id' => $data['id'],
                        'content' => $res['user_name'] . '%%手工%%已核实此订单，订单完成!!!',
                        'user_id' => $userInfo['user_id'],
                        'send_user_id' => $userInfo['user_id'],
                        'add_time' => time()
                    ]);
          			// 企业微信通知
          			$Qywx = new Qywx();
                  	if($userInfo['qywx_id']){
        				$Qywx->sendQYWXMessage($userInfo['qywx_id'],$res['user_name'] . '%%手工%%已核实此订单，订单完成!!!');
                    }
                  	// 修改订单进程状态和消息提示状态.
                  	$result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '6']);
                    return json(echoArr(200,'提交成功'));
                }
                $error = array('code' => 201, 'msg' => '订单已提交');
                return json($error);
                
            }
            $error = array('code' => 501, 'msg' => '操作失败');
            return json($error);
        } else {
            return json(echoArr(500, '非法请求'));
        }
    }
  
  	 /**
     * 组长接收业务通知
     *
     * @param $userInfo
     * @param $oId
     * @param $message
     */
    public function teamLeaderNotice($userInfo, $oId, $message){
        $user = new UserModel();
        $group = new GroupModel();
        $notice = new NoticeModel();

        $typeId = $group->where('id', $userInfo['group_id'])->value('type_id');
        $id = $user->where('group_id', $typeId)->value('user_id');

        if($id){
            $data = [
                'notice_id' => $oId,
                'content' => $userInfo['user_name'] . $message,
                'user_id' => $id,
                'send_user_id' => $userInfo['user_id'],
                'add_time' => time(),
            ];
            $notice -> isUpdate(false) -> save($data);
          
            // 企业微信通知
           	$Qywx = new Qywx();
            $QYWXUserId = Db::name('user')->where('user_id',$id)->value('qywx_id');
          	if($QYWXUserId){
            	$Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . $message);
            }
        }
    }
  
  	 /**
     * 组长接收消息
     *
     * @param $userInfo
     */
    public function teamLeader($userInfo, $data = [])
    {
        $user = new UserModel();

        $openId = '';
        if (4 != $userInfo['role_id']) {
            $group = new GroupModel();
            $typeId = $group->where('id', $userInfo['group_id'])->value('type_id');
            $openId = $user->where('group_id', $typeId)->value('no_open_id');
        }

        // 如果已绑定，则发送消息给组长
        if ($openId) {
            Message::send($openId, $data);
        }
    }

	// 驳回订单
    // order_id
    // role_id
    // confirm
    // userId
    public function orderEnd()
    {
        if ($this->request->isPost()) {
            $code = config('code');
            $msg = config('msg');
            $data = input('post.');
            if (empty($data['order_id'])) {
                $error = array('code' => $code['ParamEmpty'], 'msg' => '订单id为空');
                return json($error);
                exit;
            }
            if (empty($data['role_id'])) {
                $error = array('code' => $code['ParamEmpty'], 'msg' => '权限id为空');
                return json($error);
                exit;
            }
            $orderInfo = Db::name('order')->where('id', $data['order_id'])->find();
            $user_ids = $orderInfo['user_id'];
            $data = array_merge($orderInfo, $data);
            // 组长审核
            if ($data['role_id'] == '4'){
                // 当前用户信息
                $flag = Db::name('user') -> where('user_id', $data['userId']) -> find();
                // 组长驳回：订单驳回通知业务员
                if ($data['static'] != 1) {
                    $error = array('msg' => '订单已驳回', 'code' => 201);
                    return json($error);
                    exit;
                }
                // 消息通知业务员
                $message = [
                    '您的订单审核未通过,请及时调整!',
                    $data['order_sn'],
                    $data['username'],
                    $data['workname'],
                    '',
                    ''
                ];
                $openId = Db::table('snake_user') -> where('user_id', $data['user_id']) -> value('no_open_id');
                if ($openId) Message::send($openId, $message);
                // 业务通知：业务员
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $data['order_id'], 
                    'content' => "{$flag['user_name']}驳回了你的订单", 
                    'user_id' => $orderInfo['user_id'], 
                    'send_user_id' => $flag['user_id'], 
                    'add_time' => time()
                ]);
                Db::name('order')->where('id', $data['order_id'])->update(['static' => '3']);
                // 企业微信通知
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$data['user_id'])->value('qywx_id');
                if($QYWXUserId){
                    $Qywx->sendQYWXMessage($QYWXUserId,$flag['user_name'] . '%%业务员%%驳回你的订单');
                }
                // 修改订单进程状态和消息提示状态.
                $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '3']);
                return json(array('msg' => '驳回成功', 'code' => 200));
            }
            if ($data['role_id'] == '7' || $data['role_id'] == '9' || $data['role_id'] == '11') {
                if ($data['static'] != 4) {
                    $error = array('msg' => '订单已驳回', 'code' => 201);
                    return json($error);
                    exit;
                }
                // 手工：订单驳回通知 3d打印 / CNC / 复模
                $userInfo = Db::name('user') -> where('order_id', 'like', "%{$data['id']}%")-> where('user_id', 'neq', $data['userId'])-> find();
                $message = [
                    '您的订单审核未通过,请及时调整!',
                    $data['order_sn'],
                    $data['username'],
                    $data['workname'],
                    '',
                    ''
                ];
                if($userInfo['no_open_id']) return Message::send($userInfo['no_open_id'], $message);
                // 业务通知
                $res = Db::name('user')->where('user_id',$data['userId'])->find();
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $data['id'],
                    'content' => $res['user_name'] . '驳回你的订单，请处理',
                    'user_id' => $userInfo['user_id'],
                    'send_user_id' => $res['user_id'],
                    'add_time' => time()
                ]);
                // 企业微信通知
                $Qywx = new Qywx();
                if($userInfo['qywx_id']){
                $QYWXUserId = Db::name('user')->where('user_id',$userInfo['user_id'])->value('qywx_id');
                    $Qywx->sendQYWXMessage($QYWXUserId,$res['user_name'] . '%%生产%%驳回你的订单，请处理');
                }
                // 通知业务员
                $QYWXUserId1 = Db::name('user')->where('user_id',$user_ids)->value('qywx_id');
                if($QYWXUserId1){
                    $Qywx->sendQYWXMessage($QYWXUserId1,$res['user_name'] . '%%业务员%%驳回订单，请查看');
                }
                // 修改订单进程状态和消息提示状态.
                $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '2']);
                return json(array('msg' => '驳回成功', 'code' => 200));
            }
            // 业务员： 驳回订单 通知手工
            if($data['role_id'] == 3){
                if ($data['static'] != 5) {
                    $error = array('msg' => '订单已驳回', 'code' => 201);
                    return json($error);
                    exit;
                }
                switch ($data['confirm']) {
                    // 3d 打印
                    case 60:
                        $roleId = 7;
                        break;
                    // CNC
                    case 63:
                        $roleId = 9;
                        break;
                    // 复模
                    case 66:
                        $roleId = 11;
                        break;
                }
                $groupId = Db::name('group')->where('role_id', $roleId)->value('id');
                $userInfo = Db::name('user')->where('group_id', $groupId)->where('order_id', 'like', "%{$data['id']}%")->find();
                $message = [
                    '您的订单审核未通过,请及时调整!',
                    $data['order_sn'],
                    $data['username'],
                    $data['workname'],
                    '',
                    ''
                ];
                $res = Db::name('user')->where('user_id',$data['userId'])->find();
                if($userInfo['no_open_id']) Message::send($userInfo['no_open_id'], $message);
                // 业务通知
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $data['id'],
                    'content' => $res['user_name'] . '驳回你的订单，请处理',
                    'user_id' => $userInfo['user_id'],
                    'send_user_id' => $res['user_id'],
                    'add_time' => time()
                ]);
                // 企业微信通知
                $Qywx = new Qywx();
                if($userInfo['qywx_id']){
                    $Qywx->sendQYWXMessage($userInfo['qywx_id'],$res['user_name'] . '驳回你的订单，请处理');
                }
                $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '4']);
                return json(array('msg' => '驳回成功', 'code' => 200));
            }
            $error = array('code' => 501, 'msg' => '操作失败');
            return json($error);
        } else {
            return json(echoArr(500, '非法请求'));
        }
    }
	// 获取用户的role_id
  	public function getId(){
    	if($this->request->isAjax()){
        	$data = input('post.');
          	$userInfo = Db::name('user')->where('user_id',$data['user_id'])->value('role_id');
          	if($role_id){
            	return  json(echoArr(200, '请求成功'),$userInfo['role_id']);
            }else{
            	return  json(echoArr(500, '请求失败'));
            }
        }else{
        	return  json(echoArr(500, '非法请求'));
        }
    }
    //     public function upload(){
    //     $info = $file->validate(['size'=>2000000,'ext'=>'jpg,png,gif'])->move(env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'uploads');
    //     if($info){
    //         // Windows 转换反斜杠
    //         $img = $info->getSaveName();
    //         if("WIN" == substr(PHP_OS,0,3)){
    //             $img = str_replace('\\', '/', $img);
    //         }

    //         return echoArr(200, 'RequestSuccess', ['img' => $img]);
    //     }else{
    //         // 上传失败获取错误信息
    //         return echoArr(500, 'RequestFail');
    //     }
    // }

    //上传图片 
    // file 图片信息

    public function uploadimg()
    {


        if ($this->request->isPost()) {


            $request = Request::instance();

            $domain = $request->domain();

            $code = config('code');

            $msg = config('msg');

            $file = request()->file('file');

            if ($file) {


                $info = $file->move('public/upload/image/');

                $rule = array();


                $type = $info->check(['size' => 2000000, 'ext' => 'jpg,png,gif']);

                if (!$type) {

                    $list = array('msg' => $msg['OperationFailed'], 'code' => '图片上传失败');

                    return json($list);

                }

                if ($info) {

                    $file = array();

                    $file['src'] = $domain . '/' . 'public/upload/image/' . $info->getSaveName();

                    $list = array('data' => $file, 'msg' => $msg['RequestSuccess'], 'code' => $code['RequestSuccess']);

                    return json($list);
                } else {

                    $list = array('msg' => $msg['OperationFailed'], 'code' => '上传失败');

                    return json($list);

                }
            }


        } else {


            return json(echoArr(500, '非法请求'));

        }


    }


    // 搜索订单

    public function searchorder()
    {

        if ($this->request->isPost()) {
            $code = config('code');

            $msg = config('msg');

            $data = input('post.search');

            $orders = Db::name('order')->where('order_sn', 'like', '%' . $data . '%')->whereor('username', 'like', '%' . $data . '%')->select();

            foreach ($orders as $k => $v) {
                $orders[$k]['id'] = $v['id'];
                $orders[$k]['addTime'] = $v['addtime'];

                // var_dump($orders[$k]['id']);

                @$orders[$k]['username'] = $v['username'];
                @$orders[$k]['order_sn'] = $v['order_sn'];
                @$orders[$k]['workname'] = $v['workname'];

                @$grop_id = Db::name('group')->where('id', $v['confirm'])->find();

                // $orders[$k]['confirm'] = $grop_id['group_name'];

                $static = config('static');

                $debuff = config('debuff');

                @$orders[$k]['confirm'] = $grop_id['group_name'];

                if (isset($static[$v['static']]) && $static[$v['static']]) {
                    $orders[$k]['confirm'] .= "-{$static[$v['static']]}";
                }

                if (isset($debuff[$v['debuff']]) && $debuff[$v['debuff']]) {
                    $orders[$k]['confirm'] .= "-{$debuff[$v['debuff']]}";
                }

                @$orders[$k]['static'] = $v['static'];

                @$orders[$k]['debuff'] = $v['debuff'];

                @$uptime = config('uptime');
                @$updatatime = $uptime[$v['uptime']];
                @$orders[$k]['update'] = $v['update'] . ' ' . $updatatime;
            }

            $list = array('data' => $orders, 'msg' => $msg['RequestSuccess'], 'code' => $code['RequestSuccess']);
            return json($list);

        } else {

            return json(echoArr(500, '非法请求'));

        }

    }

}