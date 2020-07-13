<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$
return [

    // 模板参数替换
    'view_replace_str'       => array(
        '__CSS__'    => '/public/static/admin/css',
        '__JS__'     => '/public/static/admin/js',
        '__IMG__' => '/public/static/admin/images',
    ),


    'static' => [
        '1' => '待审核',
        '2' => '生产中',
        '3' => '审核失败',
        '4' => '生产中-手工',
        '5' => '订单完成',
    ],

    'debuff' => [ 
         '2' => '订单超时'
    ],

    'uptime' => [
        '上午' => '11:30',
        '下午' => '17:00',
        '晚上' => '20:00'
   ],

       'wx' => [

        'appid' => 'wx5d59065230502c01',    //微信appid
        'secret' =>'73297fbc8b5a97b84a2e3cf8c70ef0ee'      //微信secret
    ],
       'code'=>[
        'RequestSuccess'=>1001,//请求成功
        'RequestFail'=>1002,//请求失败      
        'AccessTokenError'=>1003,//token为空或者token不正确
        'ParamEmpty'=>1004,//参数为空
        'ParamError'=>1005,//参数格式不正确
        'OperationFailed'=>1006,//操作失败
        'AccessTokenOverdue'=>1007,//token过期
        'UserNotLogged'=>1008,//用户未登录
    ],
    'msg'=>[
        'RequestSuccess'=>'request is succeed',//请求成功
        'RequestFail'=>'request is failed',//请求失败
        'AccessTokenError'=>'token is not valid',//token为空或者token不正确
        'EmptyParam'=>'params is not valid',
        'ParamError'=>'params is not valid',//参数格式不正确
        'OperationFailed'=>'operation failed',//操作失败
        'AccessTokenOverdue' => 'token has expired',
        'UserNotLogged' => 'user not logged in',

    ],
];
