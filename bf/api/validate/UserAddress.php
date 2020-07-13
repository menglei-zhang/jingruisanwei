<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/12
 * Time: 17:38
 */

namespace app\index\validate;


use think\Validate;

class UserAddress extends Validate
{
    protected $rule = [
        'consignee|收件人' => 'require',
        'mobile|电话' => 'require|number',
        'address|详细地址' => 'require',
        'is_default|默认地址' => 'require',
        'province|省份' => 'require',
        'district|地区' => 'require',
        'city|城市' => 'require',
    ];
}