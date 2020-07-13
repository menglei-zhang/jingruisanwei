<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/4
 * Time: 17:09
 */

namespace app\index\validate;

use think\Validate;

class AfterSale extends Validate
{
    protected $rule = [
        'orderNum|订单号' => 'require',
        'problemDetail|问题描述' => 'require',
    ];
}