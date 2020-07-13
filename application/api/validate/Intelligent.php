<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/4
 * Time: 17:09
 */

namespace app\index\validate;

use think\Validate;

class Intelligent extends Validate
{
    protected $rule = [
        'name|姓名' => 'require|max:20',
        'height|身高' => 'require|number',
        'weight|体重' => 'require|number',
        'gender|性别' => 'require|number',
        'age|年龄' => 'require|number',
        'relationship|关系' => 'require',
        'z_img' => 'require',
        'c_img' => 'require',
        'mosaic' => 'require',
    ];

    protected $message = [

    ];

    protected $scene = [
        'info' => ['name','height','weight','gender','age','relationship','z_img','c_img','mosaic'],
        'auth' => ['openid', 'session_key','mobile'],
        'certification' => ['name', 'IDcard', 'bankCard', 'bankAccount', 'telephone'],
        'store' => ['storeId']
    ];
}