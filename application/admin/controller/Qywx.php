<?php
    
    namespace app\admin\controller;

    use think\Db;
    use app\admin\controller\WxService;
	use think\Session;

    class Qywx extends Base
    {    
      	// 企业微信 消息推送
      	public function sendQYWXMessage($userid,$content)
        {
            $token = Session::get('token');
            // return json($token);
            if(!$token){
                $WxService = new WxService;
                $res = $WxService->getAccessToken();
                $token = $res['access_token'];
            }
          	$userid = 'ZhangMengLei';
            $data = [
                "touser" => $userid, //接收用户的userid
                "msgtype" => 'miniprogram_notice',//消息类型
                "miniprogram_notice" => [
                    "appid" => 'wx5d59065230502c01',//APPid
                    // "page" => "pages/topic/detail/detail?id=1",//点击模板消息跳转至小程序的页面
                    "title" => $content,//标题
                    "content_item" => $content
                ],
            ];
            //发送消息
            $url = "https://qyapi.weixin.qq.com/cgi-bin/message/send?access_token=" . $token;
            $response = json_decode(sendCURL($url, json_encode($data,JSON_UNESCAPED_UNICODE)), true);  
            return json($response);
        }
    }