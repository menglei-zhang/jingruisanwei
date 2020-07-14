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

use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use app\admin\model\CustomerModel;

use app\admin\model\NoticeModel;

use think\Session;

use think\Db;

use lib\Upload;

use think\Request;

use app\index\controller\Message;

use app\admin\controller\Qywx;

class Order extends Base
{
    public function index()
    {
        if (request()->isAjax()) {

            $param = input('param.');
            // var_dump($param);exit;
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;


            $where = [];
            if (!empty($param)) {

                // echo 111;
                $where['order_sn'] = ['like', '%' . $param['searchOrderSn'] . '%'];
                $where['workname'] = ['like', '%' . $param['searchWorkname'] . '%'];
                $where['static'] = ['like', '%' . $param['searchStatic'] . '%'];

            }


            $user_id = Session::get('user_id');

            $user = new UserModel();

            $flag = $user->getOneUser($user_id);
            // +----------------------------------------------------------------------
            // 订单权限开始
            // 业务员    这个地方可以优化 暂时实现功能
            // +----------------------------------------------------------------------


            if ($flag['role_id'] == 3 || $flag['role_id'] == 19) {

                $where['user_id'] = $flag['user_id'];
            }

            // 销售部组长  同上
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
                array_push($a, $flag['user_id']);


                $userid = implode(',', $a);

                $where['user_id'] = array('in', $userid);

            }

            // 销售部部长
            if ($flag['role_id'] == 5) {

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

            // 编程  问题

            if ($flag['role_id'] == 6 || $flag['role_id'] == 8 || $flag['role_id'] == 10) {


                $a = array_null($flag['order_id']);

                // var_dump($a);exit;

                $where['id'] = array('in', $flag['order_id']);


            }

            // 手工
            if ($flag['role_id'] == 7 || $flag['role_id'] == 9 || $flag['role_id'] == 11) {

                $a = array_null($flag['order_id']);


                $where['id'] = array('in', $flag['order_id']);

                // $where['static'] = '4';

            }


            // 订单权限结束


            $content = new OrderModel();


            $selectResult = $content->getorderByWhere($where, $offset, $limit);


            if (empty($selectResult)) {

                $error = array('code' => '1', 'msg' => '数据为空');

                return json($error);

            }
            // $status = config('static');

            $static = config('static');

            $debuff = config('debuff');

            $uptime = config('uptime');


            foreach ($selectResult as $num => $vo) {


                $updatatime = $uptime[$vo['uptime']];

                $vo['update'] = $vo['update'] . ' ' . $updatatime;

                $grop_id = Db::name('group')->where('id', $vo['confirm'])->find();

                if (strtotime($vo['update']) < time()) {

                    if ($vo['static'] != 5) {


                        Db::name('order')->where('id', $vo['id'])->update(['debuff' => '2']);


                        @  $selectResult[$num]['static'] = $grop_id['group_name'] . '-' . $static[$vo['static']] . '-' . $debuff[$vo['debuff']];

                    } else {

                        @   $selectResult[$num]['static'] = $grop_id['group_name'] . '-' . $static[$vo['static']] . '-' . $debuff[$vo['debuff']];

                    }

                } else {


                    $selectResult[$num]['static'] = $grop_id['group_name'] . '-' . $static[$vo['static']];


                }


                $selectResult[$num]['operate'] = showOperate($this->makeButton($vo['id']));


                $all_order_id[] = $vo['id'];
            }


            $orderidall = implode(',', $all_order_id);


            // Db::name('user')->where('user_id', $flag['user_id'])->update(['order_id' => $orderidall]);


            $return['total'] = $content->getAllorder($where);

            $return['rows'] = $selectResult;

            return json($return);
        }


        return $this->fetch();
    }


    public function orderDel()
    {
        $id = input('param.id');

        $content = new OrderModel();
        $flag = $content->delOrder($id);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 上传
    public function uploadImg()
    {

        if (request()->isAjax()) {

            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            if ($info) {
                $src = '/public/upload' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(0, ['src' => $src], ''));
            } else {
                // 上传失败获取错误信息
                return json(msg(-1, '', $file->getError()));
            }
        }
    }


    //多图上传 
    public function arrayImg()
    {
        if ($this->request->isPost()) {
            $res['code'] = 1;
            $res['msg'] = '上传成功！';
            $file = $this->request->file('file');
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');

            if ($info) {

                $res['filepath'] = '/public/upload' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(0, ['src' => $res['filepath']], ''));
            } else {
                $res['code'] = 0;
                $res['msg'] = '上传失败！' . $file->getError();
            }
            return $res;
        }
    }


    public function bigarray()
    {

        if ($this->request->isPost()) {


            @$file = $this->request->file('file');

            @$param = input('param.');

            @$a = object_array($file);

            @$upload = new Upload($a[1], $param['blob_num'], $param['total_blob_num'], $param['file_name'], $param['md5_file_name']);

            @$upload_list = $upload->apiReturn();

            // $list = json_decode($upload_list);

            return $upload_list;


        }

    }

    public function downloadlist()
    {
        if ($this->request->isPost()) {

            $param = input('param.');

            // $loadlist =  $param['download'];

            $down_host = $param['download'];

            return $down_host;

        }

    }


    // 添加订单
    public function OrderAdd()
    {
        if (request()->isPost()) {
            $user = new UserModel();
            $param['update'] = '';
            $param = input('post.');
            $user_id = Session::get('user_id');
            if (!preg_match('/^[1-9]\d*|^[1-9]\d*.\d+[1-9]$/', $param['place'])) {
                return json(msg(-1, '', '只能是纯数字'));
            }
            if(!isset($param['type'])) return json(msg(-1, '', '请选择分配方式'));
            if(!isset($param['confirm'])) return json(msg(-1, '', '请选择生产部门'));
            if(1 == $param['type']) {
                if(!isset($param['program']) || !$param['program']) return json(msg(-1, '', '请选择编程人员'));
                $param['designation_user'] = $param['program'];
                unset($param['program']);
            }
            $time = strtotime('+1 day', strtotime($param['update']));
            if ($time < time()) {
                return json(msg(-1, '', '请重新选择时间'));
            }
            $day =  date('Y-m-d');
            if($day == $param['update']) {
                $currentHour = date('H');
                $currentMinute = date('i');
                switch ($param['uptime']){
                    case '上午':
                        if($currentHour > 11 || ($currentHour > 11 && $currentMinute > 30))
                            return json(msg(-1, '', '请重新选择时间'));
                        break;
                    case '下午':
                        if($currentHour > 17 || ($currentHour > 17 && $currentMinute > 0))
                            return json(msg(-1, '', '请重新选择时间'));
                        break;
                    case '晚上':
                        if($currentHour > 20 || ($currentHour > 20 && $currentMinute > 0))
                            return json(msg(-1, '', '请重新选择时间'));
                        break;
                }
            }
            $flag = $user->getOneUser($user_id);
            if ($flag['role_id'] == 4) {
                $user = explode(',', $param['workname']);
                $param['workname'] = $user[0];
                $param['user_id'] = $user[1];
            } else {
                $param['user_id'] = $user_id;
            }
            // 跟接口数据统一不然会导致接口报错
            if (!empty($param['pc_src'])) {
                foreach ($param['pc_src'] as $key => $value) {
                    $request = Request::instance();
                    $domain = $request->domain();
                    $param_list[$key] = $value;
                }
                $param['pc_src'] = $param_list;
                $param['pc_src'] = implode(',', $param['pc_src']);
            }
            // $src
            unset($param['file']);
            $param['addtime'] = date('Y-m-d H:i:s');
            $userInfo = $flag;
            $content = new OrderModel();
            $flag = $content->addorder($param);
            // 业务员：添加订单 通知给组长，业务通知
            $this -> teamLeaderNotice($userInfo, $content -> id, '添加了一个新订单');
            // 业务员：添加订单发送消息给组长
            $message = [
                '有新的订单等待您审核!',
                $param['order_sn'],
                $param['username'],
                $param['workname'],
                '',
                ''
            ];
            $this->teamLeader($userInfo, $message);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }
        $role_id = Session::get('role_id');
        // 查出指定用户的权限
        $group = new GroupModel();
        $user = new UserModel();
        $userid = Session::get('user_id');
        //  $this->assign('vo',$flag);
        $flag = $user->getOneUser($userid);
        // 查出所有客户的名字
        $customer = new CustomerModel();
        // $name = $customer->where('user_id',$flag['user_id'])->getCustomer('cust_name');
        $query = array();
        $name = array();
        if ($flag['role_id'] == 3) {
            $name = Db::name('customer')->where('user_id', $flag['user_id'])->select();
        }
        if ($flag['role_id'] == 4) {

            $children = $group->getgrouppeople($flag['group_id']);

            $a = [
                'user_id' => []
            ];
            foreach ($children as $key => $value) {

                $a['user_id'][$key] = $value['user_id'];

            }
            array_push($a['user_id'], $flag['user_id']);


            $flag['user_id'] = implode(',', $a['user_id']);

            // var_dump($flag['user_id']);

            $name = Db::name('customer')->where('user_id', 'in', $flag['user_id'])->select();

            // $user_id = $flag['user_id'];

            $query = Db::name('user')->where('user_id', 'in', $flag['user_id'])->select();

        }


        $shenchan = Db::name('group')->where('type_id', '8')->select();

        // 搜索当前王飞上一个订单编号
        $orderSn = Db::name('order') -> where('user_id', $userid) -> order('id desc') -> value('order_sn');
        if(!$orderSn) $orderSn =  'p' . str_pad(mt_rand(11111, 99999999), 8, '0', STR_PAD_LEFT);

        $this -> assign('orderSn', $orderSn);

        $this->assign('shenchan', $shenchan);

        $this->assign('real', $query);

        $this->assign('vo', $flag);

        $this->assign('name', $name);

        return $this->fetch();
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
            	$Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] .'%%组长%%添加了一个新订单');
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

        if (!$userList) return echoArr('-1', '请先添加当前部门的处理人员');

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
                    $tempData = $temp;

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

    // 修改订单
    public function orderEdit()
    {
        $content = new OrderModel();
        if (request()->isPost()) {

            $content = new OrderModel();

            $user = new UserModel();

            $param = input('post.');
            if (isset($param['pc_src']) && !$param['pc_src'][0]) unset($param['pc_src'][0]);
            $param['pc_src'] = implode(',', $param['pc_src']);
            @$confirm = $param['confirm'];

            $orderstatic = Db::name('order')->where('id', $param['id'])->find();
			$user_ids = $orderstatic['user_id'];
            $param = array_merge($orderstatic, $param);

            if(1 == $param['type']) {
                if(!isset($param['designation_user'])) {
                    if(!isset($param['program']) || !$param['program']) return json(msg(-1, '', '请选择编程人员'));

                    $param['designation_user'] = $param['program'];
                    unset($param['program']);
                }
            }
            // +----------------------------------------------------------------------
            //     如果生产部选择换成多选     换成这个
            //  foreach ($confirm as $key => $value) {

            //       $query[] = Db::table('snake_role')
            //      ->where('role_name','like',"%$value[$key]%")
            //      ->select();


            //  }

            // foreach ($query as $k => $v) {

            //       foreach ($v as $a => $j) {
            //                 $data['id'][] =  $j['id'];
            //                 $data['role_name'][] =  $j['role_name'];
            //           }
            //  }

            //  $userlist = Db::table('snake_user')->where('role_id','in',$data['id'])->field('user_id')->select();

            //  foreach ($userlist as $key => $value) {

            //      $sc_id =  Db::table('snake_user')->where('role_id',$data['user_id'])->field('user_id')->select();

            //  }

            // +----------------------------------------------------------------------

            // 总经理修改订单
//            if(16 == $param['role_id']){
//                $result = $content -> editorder($param);
//
//                return json(msg($result['code'], $result['data'], $result['msg']));
//            }

            //憨批误操作锁 业务员锁
            if ($param['role_id'] == '3') {
                $orderstatic = Db::name('order')->where('id', $param['id'])->find();
                if (5 == $orderstatic['static']) {
                    $flag = $content->editorder($param);

                    // 业务员：审核通过通知手工
                    switch ($param['confirm']) {
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
                        ->where('order_id', 'like', "%{$param['id']}%")
                        ->find();

                    $message = [
                        '您的订单已通过,请及时查看!',
                        $param['order_sn'],
                        $param['username'],
                        $param['workname'],
                        '',
                        ''
                    ];
                    if($userInfo) Message::send($userInfo['no_open_id'], $message);

                    // 业务通知
                    $sendUserInfo = $user -> find(Session::get('user_id'));

                    $notice = new NoticeModel();
                    $notice -> isUpdate(false) -> save([
                        'notice_id' => $param['id'],
                        'content' => $sendUserInfo['user_name'] . '已核实此订单，订单完成!!!',
                        'user_id' => $userInfo['user_id'],
                        'send_user_id' => $sendUserInfo['user_id'],
                        'add_time' => time()
                    ]);
                  
          			// 企业微信通知
          			$Qywx = new Qywx();
                  	$QYWXUserId = Db::name('user')->where('user_id',$userInfo['user_id'])->value('qywx_id');
                  	if($QYWXUserId){
        				$Qywx->sendQYWXMessage($QYWXUserId,$sendUserInfo['user_name'] .$userInfo['user_id']. '已核实此订单，订单完成!!!');
                    }
                    return json(msg($flag['code'], $flag['data'], $flag['msg']));
                }

                if ($orderstatic['static'] != 1 && $orderstatic['static'] != 3) {
                    $error = array('code' => '-1', 'msg' => '操作有误');

                    return json($error);
                }
            }

            // 组长提交到编程逻辑开始
            if ($param['role_id'] == '4') {
                $orderstatic = Db::name('order')->where('id', $param['id'])->find();
                //憨批误操作锁    组长锁
                //if($orderstatic['static'] > '1' && $orderstatic['static'] != '3'){
                if ($orderstatic['static'] > '1') {
                    $error = array('code' => '-1', 'msg' => '订单重复提交');
                    return json($error);
                    exit;
                }
                // 组长提交，通过审核
                if(!$param['confirm']) return json(['code' => -1, 'msg' => '业务员未填写生产部门']);
                if(!$param['type']) {
                    switch ($param['confirm']) {
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
                    $result = $this->orderHandler($roleId, $param['id']);
                    if (200 != $result['code']) return json($result);
                    $editUser = $result['data'];
                } else {
                    $editUser = $user -> find($param['designation_user']) -> toArray();
                    $temp = explode(',', $editUser['order_id']);
                    $temp[] = $param['id'];
                    $editUser['order_id'] = implode(',', $temp);
                }
                // 修改订单进程状态和消息提示状态.
                $result = Db::name('order')->where('id', $param['id'])->update(['static' => '2', 'is_view' => '2']);
                $user->isUpdate(true)->save($editUser, ['user_id' => $editUser['user_id']]);
                // 组长：通知 3D打印 / 复模 / CNC
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
                $userInfo = $user -> find(Session::get('user_id'));
                // 组长：通知财务部
                $saveData = [];
                $finance = $user->where('role_id', 13)->select();
                if ($finance) {
                    foreach ($finance as $v) {
                        $saveData[] = [
                            'notice_id' => $param['id'],
                            'content' => $userInfo['user_name'] . '已审核此订单，请查看',
                            'user_id' => $v['user_id'],
                            'send_user_id' => $userInfo['user_id'],
                            'add_time' => time()
                        ];
                        if ($v['no_open_id']) Message::send($v['no_open_id'], $message);
                        // 企业微信通知
                        $Qywx = new Qywx();
                        $QYWXUserId = Db::name('user')->where('user_id',$v['user_id'])->value('qywx_id');
                      	if($QYWXUserId){
                     		$Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . '%%财务部%%已审核此订单，请查看');
                        }
                    }
                }   
                // 企业微信通知 业务员
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$user_ids)->value('qywx_id');
                if($QYWXUserId){
                  $Qywx->sendQYWXMessage($QYWXUserId,'%%业务员%%组长已审批，请查看');
                };
                // 通知 3D打印 / 复模 / CNC
                array_push($saveData, [
                    'notice_id' => $param['id'],
                    'content' => $userInfo['user_name'] . '已审核此订单，请处理',
                    'user_id' => $editUser['user_id'],
                    'send_user_id' => $userInfo['user_id'],
                    'add_time' => time()
                ]);
				// 企业微信通知 3D打印 / 复模 / CNC
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$editUser['user_id'])->value('qywx_id');
                if($QYWXUserId){
                  $Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . '%%生产%%已审核此订单，请处理');
                }
                // 通知总经理
                $adminInfo = $user -> where('role_id', 16) -> select();
                foreach($adminInfo as $v){
                    $saveData[] = [
                        'notice_id' => $param['id'],
                        'content' => $userInfo['user_name'] . '已审核此订单，请查看（总经理%%%）',
                        'user_id' => $v['user_id'],
                        'send_user_id' => $userInfo['user_id'],
                        'add_time' => time()
                    ];
                    // 企业微信通知 3D打印 / 复模 / CNC
                    $Qywx = new Qywx();
                    $QYWXUserId = Db::name('user')->where('user_id',$v['user_id'])->value('qywx_id');
                    if($QYWXUserId){
                      $Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . '%%总经理%%已审核此订单，请查看');
                    }
                }
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> saveAll($saveData);
            }

            // 生产部编辑提交给手工
            if ($param['role_id'] == '6' || $param['role_id'] == '8' || $param['role_id'] == '10') {
                $orderstatic = Db::name('order')->where('id', $param['id'])->find();
                if ($orderstatic['static'] != 2) {
                    $error = array('code' => '-1', 'msg' => '操作有误');
                    return json($error);
                }
                switch ($param['confirm']) {
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
                $result = $this->orderHandler($roleId, $param['id']);
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
                $userInfo = $user -> find(Session::get('user_id'));

                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $param['id'],
                    'content' => $userInfo['user_name'] . '已确认过此订单，请处理',
                    'user_id' => $editUser['user_id'],
                    'send_user_id' => $userInfo['user_id'],
                    'add_time' => time()
                ]);

                $user->isUpdate(true)->save($editUser, ['user_id' => $editUser['user_id']]);
              	
              
              	// 企业微信通知 手工
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$editUser['user_id'])->value('qywx_id');
              	if($QYWXUserId){
                	$Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . '%%手工%%已确认过此订单，请处理');
                }     
                // 企业微信通知 业务员
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$user_ids)->value('qywx_id');
                if($QYWXUserId){
                  $Qywx->sendQYWXMessage($QYWXUserId,'%%业务员%%组长已审批，请查看');
                };
            }
            //憨批误操作锁    手工锁 
            if ($param['role_id'] == '7' || $param['role_id'] == '9' || $param['role_id'] == '11') {
                $orderstatic = Db::name('order')->where('id', $param['id'])->find();
                if ($orderstatic['static'] != 4) {
                    $error = array('code' => '-1', 'msg' => '操作有误');
                    return json($error);
                    exit;
                }
                // 手工：消息 通知到 业务员 审核
                $userId = Db::table('snake_order')->where('id', $param['id'])->value('user_id');
                $openId = $user -> where('user_id', $userId) -> value('no_open_id');
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
                $userInfo = $user -> find(Session::get('user_id'));

                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $param['id'],
                    'content' => $userInfo['user_name'] . '已处理完成此订单，请核实',
                    'user_id' => $userId,
                    'send_user_id' => $userInfo['user_id'],
                    'add_time' => time()
                ]);
              
              	// 企业微信通知
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$userId)->value('qywx_id');
              	if($QYWXUserId){
                	$Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . '%%业务员%%已处理完成此订单，请核实');
                }
            }


            // 业务员：审核失败后再提交
            if ($param['role_id'] == 3 || $param['static'] == 3) {
                // 通知组长
                $userInfo = $user->getOneUser($orderstatic['user_id']);

                $message = [
                    '有新的订单等待您审核!',
                    $param['order_sn'],
                    $param['username'],
                    $param['workname'],
                    '',
                    ''
                ];
                $this->teamLeader($userInfo, $message);

                // 业务通知
                $this -> teamLeaderNotice($userInfo, $orderstatic['id'], '%%组长%%再次提交了订单');

                $param['static'] = 1;
            }

            $flag = $content->editorder($param);
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }


        $data = input('param.');


        $userid = Session::get('user_id');

        $user = new UserModel();

        $group = new GroupModel();

        $flag = $user->getOneUser($userid);

        // $data['is_view'] = 1;
        // 修改消息通知状态
        if ($flag['role_id'] == '3') {

            $data['is_view'] = 6;
            $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);

        }

        if ($flag['role_id'] === 4) {

            $data['is_view'] = 2;
            $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);
        }

        if ($flag['role_id'] === 6 || $flag['role_id'] === 8 || $flag['role_id'] === 10) {

            $data['is_view'] = 4;
            $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);
        }


        if ($flag['role_id'] === 7 || $flag['role_id'] === 9 || $flag['role_id'] === 11) {

            $data['is_view'] = 5;
            $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);
        }

        // 游离于权限逻辑外的角色
        // 部长

        if ($flag['role_id'] == '5') {


            $data['is_out_role'] = '1';

            Db::name('order')->where('id', $data['id'])->update(['is_minister' => $data['is_out_role']]);

        } elseif ($flag['role_id'] == '16') {

            // 总经理

            $data['is_out_role'] = '1';

            Db::name('order')->where('id', $data['id'])->update(['is_boss' => $data['is_out_role']]);

        } elseif ($flag['role_id'] == '13 ') {

            //财务 

            $data['is_out_role'] = '1';

            Db::name('order')->where('id', $data['id'])->update(['is_finance' => $data['is_out_role']]);


        }

        $static = $content->getOneorder($data['id']);

        $userid = Session::get('user_id');

        $user = new UserModel();

        $flag = $user->getOneUser($userid);

        $flag['static'] = $static['static'];

        // ----------------
        // 留言消息通知
        // ---------------

        $message_zt = array();


        // 业务员
        if ($flag['role_id'] == '3') {

            $message_zt['is_view_message'] = 1;
        }
        // 组长
        if ($flag['role_id'] == '4') {

            $message_zt['is_minister'] = 1;

        }
        // 部长
        if ($flag['role_id'] == '5') {

            $message_zt['is_bz'] = 1;

        }
        // sla编程
        if ($flag['role_id'] == '6') {

            $message_zt['is_3d_printing'] = 1;
        }
        // 后处理
        if ($flag['role_id'] == '7') {

            $message_zt['is_3d_sg'] = 1;
        }
        // cnc
        if ($flag['role_id'] == '8') {

            $message_zt['is_cnc'] = 1;
        }
        // cnc手工
        if ($flag['role_id'] == '9') {

            $message_zt['is_cnc_sg'] = 1;
        }
        // 复模
        if ($flag['role_id'] == '10') {

            $message_zt['is_fumo'] = 1;
        }
        // 复模手工
        if ($flag['role_id'] == '11') {

            $message_zt['is_fumo_sg'] = 1;
        }
        // 财务
        if ($flag['role_id'] == '13') {

            $message_zt['is_finance'] = 1;

        }
        // 总经理
        if ($flag['role_id'] == '16') {

            $message_zt['is_boss'] = 1;

        }

        $list = Db::name('message')->where('order_id', $data['id'])->update($message_zt);


        // 查对应订单所有留言

        $message = Db::name('message')->where('order_id', $data['id'])->order('add_time', 'desc')->select();


        foreach ($message as $k => $v) {

            $message[$k]['mess_img'] = explode(',', $v['mess_img']);

        }


        // *客户名称： BUG
        $name = Db::name('customer')->select();

        $customer = new CustomerModel();


        if ($flag['role_id'] == 3) {

            $name = Db::name('customer')->where('user_id', $flag['user_id'])->select();

        }


        $a = [
            'user_id' => []
        ];

        if ($flag['role_id'] == 4) {

            $children = $group->getgrouppeople($flag['group_id']);

            foreach ($children as $key => $value) {

                $a['user_id'][$key] = $value['user_id'];

                array_push($a['user_id'], $flag['user_id']);

            }


            $flag['user_id'] = implode(',', $a['user_id']);

            // var_dump($flag['user_id']);

            $name = Db::name('customer')->where('user_id', 'in', $flag['user_id'])->select();


            $query = Db::name('user')->where('user_id', 'in', $flag['user_id'])->select();

        }


        $this->assign('message', $message);

        // 当前登录用户信息
        $this->assign('vo', $flag);

        $orderlist = $content->getOneorder($data['id']);

        if (empty($orderlist)) {

            $list = $this->error('订单号有误请联系管理人员', 'view/index');

            return $list;

            exit;
        }


        // 判断是否超时

        $uptime = config('uptime');

        $updatatime = $uptime[$orderlist['uptime']];

        $orderlist['update'] = $orderlist['update'] . ' ' . $updatatime;

        $orderlist['overtime'] = '未超出';


        if (strtotime($orderlist['update']) <= time()) {

            if ($orderlist['static'] != 5) {

                $overtime = time() - strtotime($orderlist['update']);

                $minute = $overtime / 60;         //60秒1分钟

                $hour = $minute / 60;     //60分钟1小时

                $over = round($hour);


                $orderlist['overtime'] = $over;
            }
        }

        // 指定人员
        $userList = [];
        if(1 == $orderlist['type']) $userList = $this -> programHandler($orderlist['confirm']);

        $shenchan = Db::name('group')->where('type_id', '8')->select();

        $domain = $this -> request -> domain() . '/public';

        $this -> assign('domain', $domain);

        $this->assign('shenchan', $shenchan);

        $this->assign('content', $orderlist);

        $this->assign('name', $name);

        $this -> assign('userList', $userList);

        return $this->fetch();
    }


    //修改订单状态
    public function editstatic()
    {
        if (request()->isPost()) {

            $user_id = Session::get('user_id');

            $user = new UserModel();

            $flag = $user->getOneUser($user_id);

            $param = input('post.');

            // 业务员订单驳回消息通知
            if ($flag['role_id'] == '3') {

                $param['is_view'] = '0';

                $list = Db::table('snake_order')->where('id', $param['id'])->update(['is_view' => $param['is_view']]);


            }

            //防止有憨批误操作，订单状态锁  WDNMD
            if ($flag['role_id'] == '4') {

                $list = Db::table('snake_order')->where('id', $param['id'])->field('static')->find();

                if ($list['static'] != 1) {

                    $error = array('code' => '-1', 'msg' => '操作有误');

                    return json($error);

                    exit;

                }

            }

            if ($flag['role_id'] == '7' || $flag['role_id'] == '9' || $flag['role_id'] == '11') {

                $list = Db::table('snake_order')->where('id', $param['id'])->find();

                if ($list['static'] != 4) {

                    $error = array('code' => '-1', 'msg' => '操作有误');

                    return json($error);

                    exit;

                }

                // 手工：订单驳回通知 3d打印 / CNC / 复模
                if(2 == $param['static']){
                    $userInfo = $user -> where('order_id', 'like', "%{$param['id']}%")
                            -> where('user_id', 'neq', $user_id)
                            -> find();

                    $message = [
                        '您的订单审核未通过,请及时调整!',
                        $list['order_sn'],
                        $list['username'],
                        $list['workname'],
                        '',
                        ''
                    ];
                    if($userInfo['no_open_id']) return Message::send($userInfo['no_open_id'], $message);

                    // 业务通知
                    $notice = new NoticeModel();
                    $notice -> isUpdate(false) -> save([
                        'notice_id' => $param['id'],
                        'content' => $flag['user_name'] . '驳回你的订单，请处理',
                        'user_id' => $userInfo['user_id'],
                        'send_user_id' => $flag['user_id'],
                        'add_time' => time()
                    ]);
                  
                  	// 企业微信通知
                  	$Qywx = new Qywx();
                  	$QYWXUserId = Db::name('user')->where('user_id',$userInfo['user_id'])->value('qywx_id');
                  	if($QYWXUserId){
                		$Qywx->sendQYWXMessage($QYWXUserId,$flag['user_name'] . '驳回你的订单，请处理');
                    }
                }
            }

            // 业务员： 驳回订单 通知手工
            if($flag['role_id'] == 3 && 4 == $param['static']){
                $orderInfo = Db::table('snake_order') -> where('id', $param['id']) -> find();

                switch ($orderInfo['confirm']) {
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
                        ->where('order_id', 'like', "%{$param['id']}%")
                        ->find();

                $message = [
                    '您的订单审核未通过,请及时调整!',
                    $orderInfo['order_sn'],
                    $orderInfo['username'],
                    $orderInfo['workname'],
                    '',
                    ''
                ];
                if($userInfo['no_open_id']) Message::send($userInfo['no_open_id'], $message);

                // 业务通知
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save([
                    'notice_id' => $param['id'],
                    'content' => $flag['user_name'] . '驳回你的订单，请处理',
                    'user_id' => $userInfo['user_id'],
                    'send_user_id' => $flag['user_id'],
                    'add_time' => time()
                ]);
              
              	// 企业微信通知
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$userInfo['user_id'])->value('qywx_id');
              	if($QYWXUserId){
                	$Qywx->sendQYWXMessage($QYWXUserId,$flag['user_name'] . '驳回你的订单，请处理');
            	}
            }

            $list = Db::table('snake_order')->where('id', $param['id'])->update(['static' => $param['static']]);

            // 组长驳回：订单驳回通知业务员
            if ($flag['role_id'] == '4' && 3 == $param['static']) {
                // 订单详情
                $orderInfo = Db::table('snake_order') -> find($param['id']);

                // 消息通知业务员
                $openId = $user -> where('user_id', $orderInfo['user_id']) -> value('no_open_id');
                $message = [
                    '您的订单审核未通过,请及时调整!',
                    $orderInfo['order_sn'],
                    $orderInfo['username'],
                    $orderInfo['workname'],
                    '',
                    ''
                ];
                if ($openId) Message::send($openId, $message);

                // 业务通知：业务员
                $notice = new NoticeModel();
                $notice -> isUpdate(false) -> save(['notice_id' => $param['id'], 'content' => "{$flag['user_name']}驳回了你的订单", 'user_id' => $orderInfo['user_id'], 'send_user_id' => $flag['user_id'], 'add_time' => time()]);
            	
              	// 企业微信通知
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$orderInfo['user_id'])->value('qywx_id');
              	if($QYWXUserId){
                	$Qywx->sendQYWXMessage($QYWXUserId,$flag['user_name'] . '驳回你的订单');
                }
            }

            return json(echoArr(1, '编辑订单成功', url('order/index')));
        }

    }


    //添加留言信息

    public function addmessage()
    {
        if (request()->isPost()) {

            $param = input('post.');

            $request = Request::instance();

            $domain = $request->domain();
          
          	if($param['mess_img']){
                $messImg = explode(',', $param['mess_img']);
                foreach($messImg as $k => $v){
                    $messImg[$k] = $domain . $v;
                }

                $param['mess_img'] = implode(',', $messImg);
            }

            // 留言消息通知
            if ($param['mess_list'] != "") {
                $result = db('message')->insert($param);

                $this -> messageNotice($param['user_id'], $param['order_id']);
            }
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
    public function messageNotice($userId, $orderId){
        $orderInfo = Db::table('snake_order') -> where('id', $orderId) -> find();

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
        $userList = Db::table('snake_user') -> where('order_id', 'like', "%{$orderId}%")
                -> whereOr('role_id', 13)
                -> whereOr('user_id', $orderInfo['user_id'])
                -> field('user_id,no_open_id,group_id')
                -> select();

        // 获取组长id
        $groupId = 0;
        foreach($userList as $v){
            if($v['user_id'] == $orderInfo['user_id']) $groupId = $v['group_id'];
        }
        $teamGroupId = Db::table('snake_group') -> where('id', $groupId) -> value('type_id');
        $teamInfo = Db::table('snake_user') -> where('group_id', $teamGroupId)
                -> field('user_id,no_open_id,group_id')
                -> find();
        array_push($userList, $teamInfo);

        // 发送消息给参与此订单的用户
        $saveData = [];
        foreach($userList as $v){
            // 过滤发留言的用户
            if($userId != $v['user_id']){
                if($v['no_open_id']) Message::send($v['no_open_id'], $message);

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
              
              	// 企业微信通知
                $Qywx = new Qywx();
                $QYWXUserId = Db::name('user')->where('user_id',$v['user_id'])->value('qywx_id');
              	if($QYWXUserId){
                	$Qywx->sendQYWXMessage($QYWXUserId,$userInfo['user_name'] . '发布了新留言，请查看');
                }
            }
        }

        $notice = new NoticeModel();
        $notice -> isUpdate(false) -> saveAll($saveData);
    }

    // 删除留言信息

    public function delmessage()
    {
        if (request()->isPost()) {

            $param = input('post.mess_id');


            $result = db('message')->where('mess_id', $param)->delete();

        }
    }


    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id)
    {
        return [
            '删除' => [
                'auth' => 'order/orderdel',
                'href' => "javascript:orderDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ],
            '编辑' => [
                'auth' => 'order/orderedit',
                'href' => url('order/orderEdit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],

        ];
    }

    /**
     * 编辑人员
     */
    public function programList(){
        $confirm = input("confirm", 0);

        if(!$confirm) return json(echoArr(500, '请求失败'));

        $list = $this -> programHandler($confirm);

        if(!$list) return json(echoArr(500, '没有相关的编程人员'));

        return json(echoArr(200, '请求成功', ['list' => $list]));
    }

    private function programHandler($confirm){
        switch ($confirm) {
            // 3d 打印
            case 60:
                $programeId = 62;
                break;
            // CNC
            case 63:
                $programeId = 64;
                break;
            // 复模
            case 66:
                $programeId = 67;
                break;
        }

        $user = new UserModel();
        return $user -> where('group_id', $programeId) -> field('user_id,user_name') -> select();
    }
}

