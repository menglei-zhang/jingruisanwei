<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/6
 * Time: 14:19
 */

namespace app\index\validate;


use think\Validate;

class Cart extends Validate
{
    protected $rule = [
        'cId|购物车商品id' => 'require',
        'height|身高' => 'require|number',
        'weight|体重' => 'require|number',
        'gNum|商品数量' => 'require',
        'gId|商品id' => 'require',
        'int_id|智能量体id' => 'require',
        'key' => 'require',
    ];

    protected $scene = [
        'editNum' => ['cId', 'gNum'],
        'add' => ['gId', 'gNum', 'int_id', 'key', 'height', 'weight'],
        'del' => ['cId'],
        'snatch' => ['gId'],
    ];
}