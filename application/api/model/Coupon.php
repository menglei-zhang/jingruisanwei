<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
class Coupon extends Model
{
    /**
     * 查询列表
     */
    public function resList($data){
        // 用户优惠券，条件未使用
        $couponList = new CouponList();
        $couponData = $couponList -> where('uid', $data['user_id'])
                    -> where('status', 0)
                    -> field('id,cid,type')
                    -> select()
                    -> toArray();

        // 满减和无门栏
        list($redBag, $reduce) = [0, 1];

        // 优惠券详情
        $coupon = $this -> field('id,money,condition,use_type,use_end_time') -> select() -> toArray();

        // 处理用户的优惠券
        $temp = ['reduce' => [], 'redBag' => []];
        foreach($couponData as $k => $v){
            foreach($coupon as $key => $val){
                if($v['cid'] == $val['id']){
                    // 满减
                    if($val['use_type'] == $redBag){
                        $temp['reduce'][] = [
                            'cId' => $v['id'],
                            'cPrice' => (int) $val['money'],
                            'cCondition' => (int) $val['condition'],
                            'cDate' => date('Y-m-d', $val['use_end_time'])
                        ];
                    }

                    // 无门栏
                    if($val['use_type'] == $reduce){
                        $temp['redBag'][] = [
                            'cId' => $v['id'],
                            'cPrice' => (int) $val['money'],
                        ];
                    }
                }
            }
        }

        return echoArr(200, '请求成功', $temp);
    }
}

