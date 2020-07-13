<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/1/21
 * Time: 15:20
 */

namespace app\index\model;


use think\Model;
use app\component\controller\Pay;
use app\index\validate\PayRecord as Vali;

class PayRecord extends Model
{
    /**
     * 订单支付
     */
    public function orderPay($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 订单信息
        $order = new Order();
        $info = $order -> where('user_id', $data['user_id'])
            -> field('id,order_sn,pay_status,order_amount')
            -> find($data['oId']);

        // 判断订单是否支付过
        if($info['pay_status'] != 0){
            return echoArr(500, '订单已被支付');
        }

        // 用户openId
        $user = new Users();
        $data = $user -> field('id,wx_open_id as open_id') -> find($data['user_id']);

        $wxpay = new Pay();
        // 商品描述
        $wxpay -> body = "订单{$info['order_sn']}";
        // 订单号
        $wxpay -> out_trade_no = $info['order_sn'];
        // 订单金额
        $wxpay -> total_fee = $info['order_amount'];
        // 商品id
        $wxpay -> product_id = $info['id'];
        // 商品的openId
        $wxpay -> openId = $data['open_id'];
        $res = $wxpay -> wxpay();
        $res['time'] = (string) time();

        return echoArr(200, '请求成功', $res);
    }

    /**
     * 支付后的回调
     */
    public function notify($arr){
        // 应支付金额
        $info = model('Order') -> where('order_sn', $arr['out_trade_no']) -> field('pay_status,order_amount as price') -> find();
        $info['price'] = (int) ($info['price'] * 100);

        // 判断订单是否已经支付
        if($info['pay_status'] == 1){
            return false;
        }

        // 判断支付状态
        if(isset($arr['total_fee'])){
            if($info['price'] ==  $arr['total_fee']){
                $status = 1;
                $desc = '支付成功';
            }else{
                $status = 3;
                $desc = '支付异常，支付金额和订单金额不相等';
            }
        }else{
            $status = 0;
            $desc = '支付失败';
        }

        // 支付状态
        $data['pay_status'] = $status;
        // 支付状态描述
        $data['pay_desc'] = $desc;
        // 支付订单号
        $data['transaction_id'] = $arr['transaction_id'];
        // 订单金额
        $data['order_price'] = (float) ($info['price']/100);
        // 支付金额
        $data['pay_price'] = (float) ($arr['total_fee']/100);
        // 支付时间
        $data['pay_time'] = time();
        // 支付code和支付方式名称
        $data['pay_code'] = 'weixin';
        $plugin = new Plugin();
        $data['pay_name'] = $plugin -> where('code', $data['pay_code']) -> value('name');
        // 订单号
        $data['order_sn'] = $arr['out_trade_no'];

        // 修改订单支付状态以及推荐人
        if($status == 1){
            $order =  new Order();
            $res = $order -> allowField(true) -> isUpdate(true) -> save($data, ['order_sn' => $data['order_sn']]);
        }

        // 订单支付记录
        $res = $this -> allowField(true) -> save($data);
    }
}