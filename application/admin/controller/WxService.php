<?php

    namespace app\admin\controller;

	use think\Session;

    class WxService extends Base
    {
        public function getAccessToken()
        {
            $corpid = 'ww952e7129851ba9b9';
            $corpsecret = 'K8NhMHsZuhi37aWHws0_eHclajQSBcFGBJ4F3_G3RYQ';
            // $corpsecret = '90CKNzqdfLBoNexj2-K_7w2i2zJo5ACaEgD7Cr98_yc';
            $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid={$corpid}&corpsecret={$corpsecret}";
            $response = json_decode(sendCURL($url), true);
            // return json($response);
            if ($response['errcode'] == 0){
                Session::set('token',$response["access_token"]);
                return $response;
            }
        }	
    }
?>