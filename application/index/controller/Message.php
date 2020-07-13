<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/6/14
 * Time: 17:01
 */

namespace app\index\controller;

use think\Cache;

class Message extends Base
{
    CONST APPID = 'wx0d63c0b705248ebf';
    CONST SECRET = '4e805d4e304cb2dcc242b619089d9012';

    /**
     * 获取全局 access_token
     */
    public static function globalAccessToken(){
        $accessToken = Cache::get('access_token');
        $code = 200;

        if(!$accessToken){
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=" . self::APPID . "&secret=" . self::SECRET;
            $result = sendCURL($url);
            $arr = json_decode($result, true);
            if(!isset($arr['errcode'])){
                $accessToken = $arr['access_token'];

                cache('access_token', $accessToken, 6600);
            } else {
                $code = 500;
                $accessToken = $arr;
            }
        }

        $result = echoArr($code, $code == 200 ? '请求成功' : '请求失败', $accessToken);

        return $result;
    }

    /**
     * 获取公众号 unionid
     */
    static public function getUnionId($openId){
        $result = self::globalAccessToken();
        if(200 != $result['code']) return $result;

        $url = "https://api.weixin.qq.com/cgi-bin/user/info?access_token={$result['data']}&openid=$openId&lang=zh_CN";
        $result = json_decode(sendCURL($url), true);

        $code = 500;
        if(!isset($result['errcode']) && 1 == $result['subscribe']){
            $code = 200;
        }

        return echoArr($code, $code == 200 ? '请求成功' : '请求失败', $result);
    }

    /**
     * 发送模板消息
     */
    static public function send($openId, $data){
        $result = self::globalAccessToken();
        if(200 != $result['code']) return $result;

        // 处理数据
        $temp = [];
        foreach($data as $k => $v){
            switch ($k) {
                case 0:
                    $temp['first'] = ['value' => $v];
                    break;
                case count($data) - 1:
                    $temp['remark'] = ['value' => $v];
                    break;
                default:
                    $temp['keyword' . $k] = ['value' => $v];
            }
        }

        $url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token={$result['data']}";

        $config = config('wx');
        /*$smallConfig = [
            'appid' => $config['appid'],
            'pagepath' => '/pages/homepage/homepage',
        ];*/

        $sendData = [
            'touser' => $openId,
            'template_id' => 'bz08oPbKA_SmVgYtydGM0GhwC8CMrOnfWwzYEpOAORU',
            //'miniprogram' => $smallConfig,
            'data' => $temp,
        ];
        $result = json_decode(sendCURL($url, json_encode($sendData)),true);
        if(0 == $result['errcode']){
            $code = 200;
            $msg = '请求成功';
        } else {
            $code = 500;
            $msg = '请求失败';
        }

        return echoArr($code, $msg, $result);
    }
}