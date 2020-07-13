<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/6/14
 * Time: 15:36
 */

namespace app\index\controller;


use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        header('content-type:text/html;charset=utf-8');
        $signature = input('signature', '');
        $timestamp = input('timestamp', '');
        $nonce = input('nonce', '');
        $echostr = input('echostr', '');
        $token = 'magic_jingrui';

        $arr = [$token, $timestamp, $nonce];
        sort($arr);
        $str = implode('', $arr);
        $str = sha1($str);

        if ($str == $signature) {
            if ($echostr) {
                die($echostr);
            }
        }
    }
}