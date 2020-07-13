<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/1/22
 * Time: 11:18
 */

namespace app\index\model;


use think\Model;
use app\index\validate\AfterSale as Vali;
use app\index\model\Order;

class AfterSale extends Model
{
    /**
     * 申请售后
     * @param $data
     * @return array
     */
    public function add($data){
        // 验证
        $validate = new Vali();
        if (!$validate->check($data)) {
            return echoArr(500, $validate->getError());
        }

        // 是否有此订单号
        $order = new Order();
        $res = $order -> where('user_id', $data['user_id']) -> where('order_sn', $data['orderNum']) -> value('id');
        if(!$res){
            return echoArr(500, '没有此订单号');
        }

        // 售后数据
        $temp = [
            'user_id' => $data['user_id'],
            'reason' => $data['problemDetail'],
            'order_sn' => $data['orderNum'],
            'reasonImg' => $data['imgs'],
            'add_time' => time(),
        ];

        // 增加售后记录
        $res = $this -> isUpdate(false) -> save($temp);
        if(false === $res){
            return echoArr(500, '操作失败');
        }else {
            return echoArr(200, '操作成功');
        }
    }

    /**
     * 售后记录
     */
    public function resList($data){
        $list = $this -> where('user_id', $data['user_id'])
                -> order('id desc')
                -> field('order_sn as oNumber,add_time as applyTIme,reason as problemDetail')
                -> select();

        foreach($list as $k => $v){
            $list[$k]['applyTIme'] = date('Y-m-d H:i:s', $v['applyTIme']);
        }

        return echoArr(200, '请求成功', ['list' => $list]);
    }
}