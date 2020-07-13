<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
use app\index\model\Goods;
use app\index\model\Coupon;
use app\index\model\UserAddress;
use app\component\controller\Shipping;
use app\index\model\GoodsSpecPrice;
use app\index\validate\Order as Vali;

class Order extends Model
{
    /**
     * 订单列表
     */
    public function resList($data, $domain){
        $list = $this -> where('user_id', $data['user_id'])
            -> order('id desc')
            -> field('id as oId,order_sn as orderNumber,order_status,pay_status,shipping_status,total_amount as orderAmount,shipping_price as oFreight,coupon_price as oOffer,order_amount as orderAmount')
            -> select();

        // 修改订单状态
        $ids = [];
        foreach($list as $k => $v){
            // 订单的状态判断
            switch ($v['pay_status']) {
                case 0:
                    // 初始化：未付款
                    $list[$k]['orderStatus'] = 0;
                    $list[$k]['oSstatus'] = 0;

                    if(1 == $v['order_status']){
                        // 已确认，已付款
                        $list[$k] = [
                            'orderStatus' => 1,
                            'oSstatus' => 1
                        ];
                    }
                    break;
                case 1:
                    // 初始化：已付款
                    $list[$k]['orderStatus'] = 1;
                    $list[$k]['oSstatus'] = 1;

                    if(1 == $v['order_status'] && 1 == $v['shipping_status']){
                        // 待收货
                        $list[$k]['oSstatus'] = 2;
                    }
                    break;
            }

            // 交易关闭，已收货、已完成、已取消、已废除
            if(in_array($v['order_status'], [2, 3, 4, 5])){
                $list[$k]['orderStatus'] = 2;
            }

            $ids[] = $v['oId'];

            $list[$k]['gList'] = [];
        }

        // 订单的商品
        if($ids){
            $orderGoods = new OrderGoods();
            $orderGoodsList = $orderGoods -> whereIn('order_id', $ids)
                -> field('order_id,goods_id as gId,goods_name as gName,goods_num as gNum,member_goods_price as gPrice')
                -> select()
                -> toArray();
            // 商品列表
            $ids = [];
            foreach($orderGoodsList as $k => $v){
                $ids[] = $v['gId'];
            }
            $goods = new Goods();
            $goodsList = $goods -> whereIn('id', $ids) -> field('id, original_img as gImg') -> select();

            foreach($list as $k1 => $v1){
                $temp = [];
                $num = 0;
                foreach($orderGoodsList as $k2 => $v2){
                    if($v1['oId'] == $v2['order_id']){
                        $num += $v2['gNum'];
                        $v2['gImg'] = '';
                        foreach($goodsList as $k3 => $v3){
                            if($v2['gId'] == $v3['id']){
                                $v2['gImg'] = $domain . config('imgRoute') . $v3['gImg'];
                            }
                        }
                        unset($v2['order_id']);
                        $temp[] = $v2;
                    }
                }
                $list[$k1]['oGoodsNum'] = $num;
                $list[$k1]['gList'] = $temp;
                unset($list[$k1]['order_status']);
                unset($list[$k1]['pay_status']);
                unset($list[$k1]['shipping_status']);
            }
        }

        return echoArr(200, '请求成功', $list);
    }

    public  function resFind($uid)
    {
        $data = $this->alias('p')
            ->where('user_id',$uid)
            ->join('order_goods g', 'p.id = g.order_id')
            ->join('goods s', 's.id=g.goods_id')
            ->select();
        var_dump($data->toArray());die;
        return  $data;
    }
  
    public function getGoodsAll($uid)
    {
        $goodData = $this ->where('user_id',$uid)->field('order_sn as oNumber ,order_status as oStatus ,shipping_status oSstatus,pay_status as oPstatus, shipping_price as oFreight,coupon_price as oOffer,  total_amount as oTotal, id as oId ,goods_price as gPrice')->select()->toArray();
        return $goodData;
    }


    /**
     * 取消订单
     */
     public function cancel($data)
     {
         // 验证
         $validate = new Vali();
         if(!$validate -> scene('cancel') -> check($data)){
             return echoArr(500, $validate->getError());
         }

         // 开启事务
         $goods = new Goods();
         $oGoods = new OrderGoods();
         $goods -> startTrans();
         $oGoods -> startTrans();
         $this -> startTrans();

         // 判断订单是否已被取消
         $res = $this -> where('order_sn', $data['orderNumber'])
                -> where('user_id', $data['user_id'])
                -> field('id,order_status')
                -> find() -> toArray();
         if(!$res){
             return echoArr(500, '该订单不存在');
         }

         // 操作此订单的条件
         $where['order_sn'] = $data['orderNumber'];

         // 判断订单是否取消订单还是删除订单
         if(3 == $res['order_status']){
             // 删除订单内的商品
             $result = $oGoods -> where('order_id', $res['id']) -> delete();
             if(false === $result){
                return echoArr(500, '删除失败');
             }

             // 删除此订单
             $res = $this -> destroy($res['id']);
         }else{
             // 取消订单，还原商品销量
             $result = $this -> returnGoods($goods, $oGoods, $res['id']);
             if($result['code'] == 500){
                 // 回滚
                 $goods -> rollback();
                 $oGoods -> rollback();

                 return $result;
             }

             $res = $this -> isUpdate(true) -> save(['order_status' => 3], $where);
         }

         if(false === $res){
             // 回滚
             $goods -> rollback();
             $oGoods -> rollback();
             $this -> rollback();

             return echoArr(500, '操作失败');
         }

         // 提交
         $goods -> commit();
         $oGoods -> commit();
         $this -> commit();

         return echoArr(200, '请求成功');
     }

    /**
     * 还原订单商品的销量
     */
    public function returnGoods($goods, $oGoods, $oId){
        // 订单内的商品
        $oGoodsList = $oGoods -> where('order_id', $oId) -> field('id,goods_id,goods_num') -> select() -> toArray();

        // 商品的销量
        $ids = [];
        if($oGoodsList){
            $ids = array_column($oGoodsList, 'goods_id');
        }
        $goodsList = $goods -> whereIn('id', $ids) -> field('id,sales_sum') -> select() -> toArray();

        // 计算商品销量
        $editData = array_reduce($goodsList, function($result, $item) use($oGoodsList){
            foreach($oGoodsList as $k => $v){
                if($item['id'] == $v['goods_id']){
                    $item['sales_sum'] += $v['goods_num'];
                }
            }

            $result[] = $item;

            return $result;
        });

        // 还原商品销量
        $res = $goods -> isUpdate(true) -> saveAll($editData);
        if(false === $res){
            $goods -> rollback();

            return echoArr(500, '操作失败');
        }

        return echoArr(200, '操作成功');
    }

    /**
     * 生成订单
     */
    public function addOrder($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('generate') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        $user = new Users();
        $oGoods = new OrderGoods();
        $cart = new Cart();
        $coupon = new Coupon();
        $couponList = new CouponList();

        // 开启事务
        $this -> startTrans();
        $user -> startTrans();
        $oGoods -> startTrans();
        $cart -> startTrans();
        $coupon -> startTrans();
        $couponList -> startTrans();

        // 删除购物车内的商品
        $res = $this -> delCart($data['gList'], $data['user_id']);

        // 判断删除购物车商品是否出错
        if($res['code'] != 200){
            return $res;
        }

        // 商品信息
        $data['goods'] = $res['data'];

        // 收货地址
        $address = new UserAddress();
        $order = $address -> field('consignee,province,city,district,address,mobile,zipcode') -> find($data['addressId']) -> toArray();

        // 用户id
        $order['user_id'] = $data['user_id'];

        // 订单支付方式
        $order['pay_code'] = 'weixin';
        $order['pay_name'] = '微信';
        $order['pay_status'] = 0;

        // 订单生成时间
        $order['add_time'] = time();

        // 用户备注
        $order['user_note'] = $data['user_note'];

        // 订单号
        $order['order_sn'] = random_num($data['user_id']);

        // 订单价格，返回值为订单内的商品
        $orderGoods = $this -> goodsPrice($data, $order);

        // 错误信息判断
        if(isset($orderGoods['code'])){
            return $orderGoods;
        }

        // 生成订单
        $res = $this -> isUpdate(false) -> save($order);
        if(false === $res){
            $this -> rollback();
            $user -> rollback();
            $oGoods -> rollback();
            $cart -> rollback();
            $coupon -> rollback();
            $couponList -> rollback();

            return echoArr(500, '操作失败');
        }

        // 生成订单商品
        foreach($orderGoods as $k => $v){
            $orderGoods[$k]['order_id'] = $this -> id;
        }
        $res = $oGoods -> isUpdate(false) -> saveAll($orderGoods);
        if(false === $res){
            $this -> rollback();
            $user -> rollback();
            $oGoods -> rollback();
            $cart -> rollback();
            $coupon -> rollback();
            $couponList -> rollback();

            return echoArr(500, '操作失败');
        }

        // 提交事务
        $this -> commit();
        $user -> commit();
        $oGoods -> commit();
        $cart -> commit();
        $coupon -> commit();
        $couponList -> commit();

        $temp = [
            'oId' => $this -> id,
            'isPay' => 0,
        ];

        // 判断是否用优惠券抵扣呢
        if($order['pay_status']){
            $temp['isPay'] = 1;
        }

        return echoArr(200, '操作成功', $temp);
    }

    /**
     * 商品价格
     */
    public function goodsPrice($data, &$order){
        // 商品id
        $ids = [];
        foreach($data['goods'] as $k => $v){
            $ids[] = $v['gId'];
        }

        // 获取订单的所有商品
        $goods = new Goods();
        $list = $goods -> whereIn('id', $ids) -> field('id,goods_name,goods_sn,sales_sum,shop_price') -> select();

        // 会员折扣
        $user = new Users();
        $userInfo = $user -> where('id', $data['user_id']) -> field('level') -> find();
        $level = new UserLevel();
        $discount = $level -> where('id', $userInfo['level']) -> value('discount');

        // 订单商品表
        $orderGoods = [];
        $gPrice = 0;
        $goodsSum = [];
        foreach($data['goods'] as $k => $v){
            foreach($list as $key => $val){
                if($v['gId'] == $val['id']){
                    // 商品总价
                    $gPrice += $val['shop_price'] * $v['gNum'];

                    // 商品销量
                    $goodsSum[] = [
                        'id' => $val['id'],
                        'sales_sum' => $val['sales_sum'] + $v['gNum']
                    ];

                    // 记录订单内的商品
                    $orderGoods[] = [
                        'goods_id' => $val['id'],
                        'goods_name' => $val['goods_name'],
                        'goods_sn' => $val['goods_sn'],
                        'goods_num' => $v['gNum'],
                        'goods_price' => $val['shop_price'],
                        'member_goods_price' => $val['shop_price'] * ($discount / 100),
                        'spec_key' => $v['specId'],
                        'spec_key_name' => $v['specName'],
                        'mend_id' => $v['mend_id'],
                        'mend_name' => $v['mend_name'],
                        'activity_name' => $v['activity_name'],
                        'height' => $v['height'],
                        'weight' => $v['weight'],
                    ];
                }
            }
        }

        // 邮费
        $order['shipping_price'] = 0;
        // 商品总价
        $order['goods_price'] = $gPrice;
        // 订单总价
        $order['total_amount'] = $gPrice + $order['shipping_price'];
        // 应付价格
        $order['order_amount'] = $order['total_amount'];

        // 优惠券抵扣
        $couponMoney = 0;
        $couponEdit = [];
        $couponListEdit = [];
        $couponList = new CouponList();
        if($data['couponId']){
            $couponIds = explode('_', $data['couponId']);

            // 用户拥有的优惠券
            $couponData = $couponList -> whereIn('id', $couponIds)
                        -> where('status', 0)
                        -> where('uid', $data['user_id'])
                        -> select();

            // 优惠券列表
            $coupon = new Coupon();
            $coupons = $coupon -> select();

            // 优惠券金额
            foreach($couponData as $k => $v){
                foreach($coupons as $key => $val){
                    if($v['cid'] == $val['id']){
                        // 优惠券使用时间是否到期
                        if($val['use_end_time'] > time()){
                            // 满减券
                            $edit = true;
                            if($val['use_type'] == 0){
                                // 是否满足使用条件
                                if($gPrice >= $val['condition']){
                                    $couponMoney += $val['money'];
                                }else{
                                    $edit = false;
                                }
                            }

                            // 无门槛
                            if($val['use_type'] == 1){
                                $couponMoney += $val['money'];
                            }

                            // 是否满足条件，需修改的优惠券
                            if($edit){
                                // 更改用户的使用状态
                                $couponListEdit[] = [
                                    'id' => $v['id'],
                                    'status' => 1,
                                    'use_time' => time()
                                ];

                                // 更改优惠券的使用数量
                                if(!isset($couponEdit[$val['id']])){
                                    $couponEdit[$val['id']] = [
                                        'id' => $val['id'],
                                        'use_num' => $val['use_num']
                                    ];
                                }

                                $couponEdit[$val['id']]['use_num'] += 1;
                            }
                        }
                    }
                }
            }
        }

        // 优惠券抵扣
        $order['coupon_price'] = $couponMoney;

        // 判断优惠券是否足够支付此订单
        if($couponMoney >= $order['order_amount']){
            // 更改订单支付状态
            $order['pay_status'] = 1;
            $order['pay_time'] = time();

            // 应付订单价格
            $order['order_amount'] = 0;
        }else{
            // 优惠券金额抵扣，剩余应付订单价格
            $order['order_amount'] -= $couponMoney;
        }

        // 将当前优惠券数量更改
        $res = $couponList -> isUpdate(true) -> saveAll($couponEdit);
        if(false === $res){
            return echoArr(500, "操作失败");
        }

        // 将当前订单用户所用的优惠券更改为已使用
        $res = $couponList -> isUpdate(true) -> saveAll($couponListEdit);
        if(false === $res){
            return echoArr(500, "操作失败");
        }

        // 修改商品的销量
        $res = $goods -> isUpdate(true) -> saveAll($goodsSum);
        if(false === $res){
            return echoArr(500, "操作失败");
        }

        return $orderGoods;
    }

    /**
     * 删除购物车商品
     */
    public function delCart($data, $userId){
        // 转换成数组
        $temp = json_decode($data, true);

        // 购物车id
        $ids = [];
        foreach($temp as $k => $v){
            $ids[] = $v['gId'];
        }

        $cart = new Cart();
        $list = $cart -> where('user_id', $userId)
                -> whereIn('id', $ids)
                -> field('id,activity_name,goods_id,spec_key,goods_num,spec_key_name,intelligent_id,height,weight')
                -> select() -> toArray();

        // 量体id
        $intelligentIds = array_column($list, 'intelligent_id');
        $intelligent = new Intelligent();
        $intelligentData = $intelligent -> whereIn('id', $intelligentIds) -> field('id,img_name') -> select();

        // 商品Id
        $goods = [];
        foreach($list as $k => $v){
            $name = '';
            foreach($intelligentData as $key => $val){
                if($v['intelligent_id'] == $val['id']){
                    $name = $val['img_name'];
                }
            }

            $gNum = 0;
            foreach($temp as $key => $val){
                if($val['gId'] == $v['id']){
                    $gNum = $val['gNum'];
                }
            }

            $goods[] = [
                'gId' => $v['goods_id'],
                'gNum' => $gNum ? $gNum : $v['goods_num'],
                'specId' => $v['spec_key'],
                'specName' => $v['spec_key_name'],
                'mend_id' => $v['intelligent_id'],
                'mend_name' => $name,
                'activity_name' => $v['activity_name'],
                'height' => $v['height'],
                'weight' => $v['weight'],
            ];
        }

        // 判断购物车是否有此商品
        if($ids){
            $cart -> destroy($ids);
        }

        return echoArr(200, '操作成功', $goods);
    }

    /**
     * 订单详情
     */
    public function detail($data, $domain){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('detail') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 订单信息
        $res = [];
        $info = $this -> where('user_id', $data['user_id'])
            -> where('id', $data['oId'])
            -> field('id,order_sn,add_time,pay_name,pay_time,total_amount,shipping_price,coupon_price,goods_price,order_amount,user_note,consignee,province,city,district,address,mobile,shipping_name')
            -> find();

        // 当前用户和订单不匹配
        if(!$info){
            return echoArr(500, '非法操作');
        }

        // 订单内的商品
        $oGoods = new OrderGoods();
        $list = $oGoods -> where('order_id', $info['id'])
            -> field('goods_id as gId,goods_num as gNum,goods_price as gPrice,goods_name as gName,mend_name as gSize')
            -> select();

        // 订单的商品id和商品总数
        list($ids, $num) = [[], 0];
        foreach($list as $k => $v){
            $num++;
            $ids[] = $v['gId'];
        }

        // 订单商品列表
        $goods = new Goods();
        $gList = $goods -> whereIn('id', $ids) -> field('original_img as gImg,id') -> select();
        foreach($list as $k => $v){
            foreach($gList as $key => $val){
                if($v['gId'] == $val['id']){
                    $list[$k]['gImg'] = $domain . config('imgRoute') . $val['gImg'];
                }
            }
        }
        $res['gList'] = $list;

        // 地址
        $address = [
            'recipient' => $info['consignee'],
            'aDetail' => "{$info['province']} {$info['city']} {$info['district']} {$info['address']}",
            'phone' => $info['mobile']
        ];
        $res['address'] = $address;

        // 订单支付时间
        $oPtime = '';
        if($info['pay_time']){
            $oPtime = date('Y-m-d H:i:s', $info['pay_time']);
        }
        $res['oPTime'] = $oPtime;

        // 订单是否使用优惠券
        $res['oOffer'] = $info['coupon_price'];
        $res['oCoupon'] = 0;
        if($info['coupon_price'] > 0){
            $res['oCoupon'] = 1;
        }

        // 订单其他信息
        $res['oNum'] = $info['order_sn'];
        $res['oTime'] = date('Y-m-d H:i:s', $info['add_time']);
        $res['oTotal'] = $info['order_amount'];
        $res['oFreight'] = $info['shipping_price'];

        return echoArr(200, '请求成功', $res);
    }

    /**
     * 物流状态
     */
    public function logistics($data, $domain){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('logistics') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 验证是否为该用户的订单
        $oId = $this -> where('order_sn', $data['orderNumber']) -> where('user_id', $data['user_id']) -> value('id');
        if(!$oId){
            return echoArr(500, '该订单不存在');
        }

        // 获取物流单号
        $delivery = new Delivery();
        $where['user_id'] = $data['user_id'];
        $where['order_sn'] = $data['orderNumber'];
        $info = $delivery -> where($where) -> field('shipping_code,shipping_name,invoice_no') -> find();
        if(!$info){
            return echoArr(500, '没有物流订单');
        }

        // 物流信息
        $shipping = new Shipping();
        // 快递编码
        $shipping -> com = $info['shipping_code'];
        // 物流单号
        $shipping -> no = $info['invoice_no'];
        $res = $shipping -> kdniao();

        // 物流状态
        if($res['data']['status'] == 0){
            $status = 1;
        }else if($res['data']['status'] == 1){
            $status = 2;
        }
        $temp['logisticsStatus'] = $status;
        $temp['trackingNumber'] = $info['invoice_no'];
        $temp['logisticsCompany'] = $info['shipping_name'];
        $temp['logisticsInfo'] = $res['data']['list'];

        // 订单内的商品
        $oGoods = new OrderGoods();
        $gInfo = $oGoods -> where('order_id', $oId)
                -> order('id asc')
                -> field('goods_name,goods_id,goods_num')
                -> find()
                -> toArray();

        // 商品主图
        $goods = new Goods();
        $img = $goods -> where('id', $gInfo['goods_id']) -> value('original_img');
        $route = '';
        if($img){
            $route = $domain . config('imgRoute') . $img;
        }

        // 返回的商品数据
        $temp['goods'] = [
            'img' => $route,
            'name' => $gInfo['goods_name']
        ];
        $temp['orderNum'] = $data['orderNumber'];

        return echoArr(200, '请求成功', $temp);
    }

    /**
     * 确认收货
     */
    public function confirm($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('confirm') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 判断该订单是否正常
        $result = $this -> where('order_sn', $data['orderNumber'])
            -> where('user_id', $data['user_id'])
            -> field('order_status,shipping_status,pay_status')
            -> find();
        if(!$result){
            return echoArr(500, '网络错误');
        }

        // 是否为已发货的订单
        if(1 != $result['order_status'] || 1 != $result['pay_status'] || 1 != $result['shipping_status']){
            return echoArr(500, '网络错误');
        }

        // 更新的条件
        $where['order_sn'] = $data['orderNumber'];
        $where['user_id'] = $data['user_id'];

        $temp['order_status'] = 4;
        $temp['confirm_time'] = time();
        $res = $this -> isUpdate(true) -> save($temp, $where);
        if(false === $res){
            return echoArr(500, '操作失败');
        }

        return echoArr(200, '请求成功');
    }
}

