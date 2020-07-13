<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/9
 * Time: 20:56
 */

namespace app\index\validate;


use think\Validate;

class Order extends Validate
{
    protected $rule = [
        'orderNumber|订单编号' => 'require',
        'addressId|地址id' => 'require|number',
        'gList|商品列表' => 'require',
        'oId|订单id' => 'require|number',
        'isCart|判断购物车提交' => 'require'
    ];

    protected $scene = [
        'cancel' => ['orderNumber'],
        'confirm' => ['orderNumber'],
        'logistics' => ['orderNumber'],
        'generate' => ['addressId', 'gList'],
        'detail' => ['oId']
    ];
}