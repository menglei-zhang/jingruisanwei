<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/4
 * Time: 18:35
 */

namespace app\index\model;

use think\Model;
use app\index\validate\Users as Vali;
use app\index\model\UserLevel;
use think\facade\Cache;
use app\index\model\BonusRecord;
use app\index\model\Order;
use app\component\controller\Ddysms;
use app\index\model\ServiceStore;
use app\index\controller\Wx;

class Users extends Model
{
    /**
     * 创建用户，获取token
     */
    public function form($info)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('auth')->check($info)) {
            return echoArr(500, $validate->getError());
        }
        $temp['token'] = encryption($info['session_key'] . $info['openid']);
        $temp['wx_open_id'] = $info['openid'];
        // 判断用户是否存在
        $isUser = $this->where('wx_open_id', $info['openid'])->value('id');

        // 不存在则增加用户
        if (!$isUser) {
            $res = $this->allowField(true)->isUpdate(false)->save($temp);
            if (false === $res) {
              return echoArr(500, '登录失败');
            }
         }

        // 存放缓存
        Cache::set($temp['token'], $temp['wx_open_id'], 21600);
        return echoArr(200, '登录成功', ['token' => $temp['token']]);
    }
  

  	/**
    * 完善用户信息
    */
    public function editUserInfo($data)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('userInfo')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        $data['user_info'] = json_decode($data['userInfo'], true);
        $temp['head_pic'] = $data['user_info']['avatarUrl'];
        $temp['sex'] = $data['user_info']['gender'];
        $temp['username'] = $data['user_info']['nickName'];
        // 微信配置信息
        $config['iv'] = $data['iv'];
        $config['encryptedData'] = $data['encryptedData'];
        $config['signature'] = $data['signature'];
        $temp['config'] = serialize($config);
        $temp['reg_time'] = time();

        $temp['id'] = $data['user_id'];

        $result = $this->isUpdate(true)->save($temp);
        if(false === $result){
            return echoArr(500, '操作失败');
        }
        return echoArr(200, '操作成功');
    }

    /**
     * 发送短信验证码
     */
    public function sendCode($data)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('code')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        // 短信验证码
        $sms = new Ddysms();
        $sms->receive_phone = $data['telephone'];
        $res = $sms->aliSms();

        return $res;
    }

    /**
     * 新人红包
     */
    public function redbag($data){
        // 判断是否为领取还是查询
        if(!isset($data['receive'])){
            return echoArr(500, '参数错误');
        }

        // 领取红包
        $coupon = new Coupon();
        $coupons = new CouponList();

        // 新人红包
        $cId = 5;

        // 判断红包是否领取
        $status = 0;
        $result = $coupons -> where('uid', $data['user_id']) -> where('cid', $cId) -> value('id');
        if($result){
            $status = 1;
        }

        // 此次请求为查询，则返回结果
        if(!$data['receive']){
            return echoArr(200, '请求成功', ['status' => $status, 'userId' => $data['user_id']]);
        }

        // 领取红包时，判断红包是否已被领取
        if($result){
            return echoArr(500, '红包已领取');
        }

        // 开启事务
        $coupon -> startTrans();
        $coupons -> startTrans();

        // 判断新人红包是否开启
        $bag = $coupon -> where('id', $cId) -> where('status', 1) -> find();
        if(!$bag){
            return echoArr(500, '此活动已结束');
        }

        // 新人红包数量是否足够
        if($bag['createnum'] == 0){
            return echoArr(500, '红包数量不足');
        }

        // 红包数据
        $temp = [
            'cid' => $cId,
            'type' => $bag['type'],
            'uid' => $data['user_id'],
            'send_time' => time(),
        ];

        // 用户领取红包
        $result = $coupons -> isUpdate(false) -> save($temp);
        if(false === $result){
            $coupons -> rollback();

            return echoArr(500, '抱歉，此活动正在维护');
        }

        // 优惠券领取数量
        $temp = [
            'id' => $cId,
            'send_num' => $bag['send_num'] + 1
        ];
        $result = $coupon -> isUpdate(true) -> save($temp);
        if(false === $result){
            $coupons -> rollback();
            $coupon -> rollback();

            return echoArr(500, '抱歉，此活动正在维护');
        }

        $coupons -> commit();
        $coupon -> commit();

        return echoArr(200, '领取成功');
    }

    /**
     * 推荐红包
     */
    public function recommend($data){
        // 验证
        $validate = new Vali();
        if (!$validate->scene('recommend')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        // 红包id
        $cId = 6;
        // 红包数量
        $num = 2;

        // 推荐红包
        $coupon = new Coupon();
        $coupons = new CouponList();

        // 开启事务
        $this -> startTrans();
        $coupon -> startTrans();
        $coupons -> startTrans();

        // 判断自己领取自己的红包
        if($data['userId'] == $data['user_id']){
            return echoArr(500, '有意思吗，骚年，还自己领取自已的红包');
        }

        // 判断是否为重复领取
        $isPid = $this -> where('id', $data['user_id']) -> value('pid');

        // 判断是否有上级
        if($isPid){
            return echoArr(500, '抱歉，您已经领取过了');
        }

        // 赠送人的上一级，二级关系
        $giveIds = $data['userId'];
        $pId = $this -> where('id', $giveIds) -> value('pid');

        // 推荐红包具体数据
        $couponData = $coupon -> find($cId);

        // 判断推荐红包是否开启
        if($couponData['createnum'] == 0){
            return echoArr(500, '此活动已结束');
        }

        // 推荐红包数量是否足够
        if($couponData['createnum'] == 0 || $couponData['createnum'] - $num >= 0){
            return echoArr(500, '红包数量不足');
        }

        // 领取红包
        $temp = [
            // 接收人领取红包
            ['cid' => $cId, 'type' => $couponData['type'], 'uid' => $data['user_id'], 'send_time' => time()],

            // 赠送人领取红包
            ['cid' => $cId, 'type' => $couponData['type'], 'uid' => $data['userId'], 'send_time' => time()],
        ];

        // 判断赠送人是否有上一级，有则领取一个红包
        if($pId){
            $num++;
            $temp[] = ['cid' => $cId, 'type' => $couponData['type'], 'uid' => $pId, 'send_time' => time()];
        }

        // 更改数据
        $res = $coupons -> isUpdate(false) -> saveAll($temp);
        if(false === $res){
            $coupon -> rollback();

            return echoArr(500, '抱歉，此活动正在维护');
        }

        // 更改优惠券领取数量
        $res = $coupon -> isUpdate(true) -> save(['id' => $cId, 'send_num' => $couponData['send_num'] + $num]);
        if(false === $res){
            $coupons -> rollback();
            $coupon -> rollback();

            return echoArr(500, '抱歉，此活动正在维护');
        }

        // 更改用户领取记录
        $userEdit = [];
        if(!$pId){
            $userEdit[] = ['id' => $giveIds, 'pid' => -1];
        }
        $userEdit[] = ['id' => $data['user_id'], 'pid' => $giveIds];
        $res = $this -> isUpdate(true) -> saveAll($userEdit);
        if(false === $res){
            $this -> rollback();
            $coupons -> rollback();
            $coupon -> rollback();

            return echoArr(500, '抱歉，此活动正在维护');
        }

        $this -> commit();
        $coupons -> commit();
        $coupon -> commit();

        return echoArr(200, '领取成功');
    }

    /**
     * 切换账号
     */
    public function switchAccount($data)
    {
        // 验证
        if (!isset($data['telephone'])) {
            return echoArr(500, '没有该子账号');
        }
        if (!$data['telephone']) {
            return echoArr(500, '没有该子账号');
        }

        // 主账号切换子账号
        // 判断是否是当前主账号的子账号
        $where1 = [
            ['mobile', 'eq', $data['telephone']],
            ['pid', 'eq', $data['user_id']],
        ];
        $query = $this->where($where1);

        // 判断是否是当前子账号的主账号
        $pid = $this->where('id', $data['user_id'])->value('pid');
        if ($pid) {
            $where2 = [
                ['mobile', 'eq', $data['telephone']],
                ['id', 'eq', $pid],
            ];

            $query->whereOr($where2);
        }
        $isAccoutn = $query->field('id,open_id')->find();

        if ($isAccoutn) {
            // 生成token
            $openId = $this->where('id', $data['user_id'])->value('open_id');
            $token = encryption(uniqid() . $openId);

            // 存放缓存
            Cache::set($token, $isAccoutn['open_id'], 21600);

            return echoArr(200, '请求成功', ['token' => $token]);
        } else {
            return echoArr(500, '没有该子账号');
        }
    }

    /**
     * 用户基本信息
     */
    public function details($data, $domain)
    {
        $info = $this
            ->field('username as name,mobile as account,contribution,head_pic as avatar,level,balance as numOfCoupons,id,pid')
            ->order('pid asc')
            ->select();

        // 账号信息和子账号
        $user = [];
        $account = [];
        foreach ($info as $k => $v) {
            // 当前子账号的主账号
            if ($v['id'] == $data['user_id']) {
                if ($v['pid']) {
                    foreach ($info as $key => $val) {
                        if ($v['pid'] == $val['id']) {
                            $account[] = $val['account'];
                        }
                    }
                }
            }

            // 当前主账号和子账号的列表信息
            if ($v['id'] == $data['user_id'] || $v['pid'] == $data['user_id']) {
                $account[] = $v['account'];

                if ($v['id'] = $data['user_id']) {
                    $user = $v;

                    // 等级图标
                    $level = new UserLevel();
                    $user['level'] = $domain . config('imgRoute') . $level->where('id', $v['level'])->value('icon');
                }
            }
        }
        $user['account'] = $account;
        unset($user['id']);
        unset($user['pid']);

        return echoArr(200, '请求成功', $user);
    }

    /**
     * 录入实名认证信息
     */
    public function certification($data)
    {
        // 验证
        $validate = new Vali();
        if (!$validate->scene('certification')->check($data)) {
            return echoArr(500, $validate->getError());
        }

        // 完善信息
        $temp['id'] = $data['user_id'];
        unset($data['user_id']);
        $temp['real_name'] = serialize($data);

        $result = $this->allowField(true)->isUpdate(true)->save($temp);
        if (false === $result) {
            return echoArr(500, '请求失败');
        }

        return echoArr(200, '请求成功');
    }

    /**
     * 购物券列表和消费记录
     */
    public function shoppingVoucher($data)
    {
        // 购物车记录列表
        $bonusList = new BonusRecord();
        $where = [
            'user_id' => $data['user_id'],
            'type' => 1
        ];
        $shoppingVoucher = $bonusList->where($where)->field('count_price as price,times as date')->select();
        foreach ($shoppingVoucher as $k => $v) {
            $shoppingVoucher[$k]['date'] = date('Y-m-d', $v['date']);
        }
        $temp['shoppingVoucher'] = $shoppingVoucher;

        // 消费记录列表
        $order = new Order();
        $temp['expensesRecord'] = $order->where('user_id', $data['user_id'])
            ->where('shopping_price', '>', 0)
            ->field('shopping_price as price,order_sn as numbering')
            ->select();

        return echoArr(200, '请求成功', $temp);
    }

    /**
     * 我的人脉
     */
    public function connections($data, $domain)
    {
        $data['user_id'] = 2;
        // 所有用户
        $users = $this->field('head_pic as img,username as name,mobile as account,referee_id,id,level')->select();

        // 用户等级
        $level = new UserLevel();
        $levelList = $level->field('id,level_name as elName,icon as elImg')->select();

        $valuable = [];
        $temp['valuable'] = [];
        foreach ($users as $k => $v) {
            // 用户等级图标和名称
            foreach ($levelList as $k1 => $v1) {
                if ($v['level'] == $v1['id']) {
                    $users[$k]['elImg'] = $domain . config('imgRoute') . $v1['elImg'];
                    $users[$k]['elName'] = $v1['elName'];
                }
            }

            // 我的贵人
            if ($v['id'] == $data['user_id']) {
                $magnate = [];
                foreach ($users as $key => $val) {
                    if ($v['referee_id'] == $val['id']) {
                        $magnate['img'] = '';
                        if ($val['img']) {
                            $magnate['img'] = $domain . config('imgRoute') . $val['img'];
                        }
                        $magnate['name'] = $val['name'];
                        $magnate['elImg'] = $val['elImg'];
                        $magnate['elName'] = $val['elName'];
                        $magnate['account'] = $val['account'];
                        $magnate['people'] = [];

                        // 他的人脉
                        list($vip, $member) = [0, 0];
                        foreach ($users as $k2 => $v2) {
                            if ($val['id'] == $v2['referee_id']) {
                                switch ($v2['level']) {
                                    case 5:
                                        $member++;
                                        break;
                                    case 6:
                                        $vip++;
                                        break;
                                }
                            }
                        }
                        $magnate['people']['vip'] = $vip;
                        $magnate['people']['member'] = $member;
                    }
                }
                $temp['valuable'] = $magnate;
            }

            // 我的人脉
            if ($v['referee_id'] == $data['user_id']) {
                $people = [];
                $people['img'] = '';
                if ($v['img']) {
                    $people['img'] = $domain . config('imgRoute') . $v['img'];
                }
                $people['name'] = $v['name'];
                $people['elImg'] = $v['elImg'];
                $people['elName'] = $v['elName'];
                $people['account'] = $v['account'];
                $people['people'] = [];

                // 他的人脉
                list($vip, $member) = [0, 0];
                foreach ($users as $key => $val) {
                    if ($v['id'] == $val['referee_id']) {
                        switch ($val['level']) {
                            case 5:
                                $member++;
                                break;
                            case 6:
                                $vip++;
                                break;
                        }
                    }
                }
                $people['people']['vip'] = $vip;
                $people['people']['member'] = $member;
                $valuable[] = $people;
            }
        }
        $temp['myPeople'] = $valuable;

        return echoArr(200, '请求成功', $temp);
    }
  
  
    //更改用户手机号，
    public  function upMobil($mobile,$openid)
    {
        $res = ['mobile'=>$mobile];
        $data = $this -> save($res, ['wx_open_id' => $openid]);
        return $data;
    }

    public  function getUserFind($openid)
    {
         $data = $this->where('wx_open_id',$openid)->field('id,username as uName ,head_pic as uAvatar,mobile as uPhone')->find()->toArray();
         return $data;
    }

    public function getUserId ($openid)
    {
        $id = $this->where('wx_open_id',$openid)->field('id')->find()->toArray();
        return $id ;
    }
}