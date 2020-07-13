<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/6/18
 * Time: 17:08
 */

namespace app\index\controller;

use app\index\model\User;
class Event extends Base
{
    public function message(){
        //接受用户的事件或消息
        $postdata = file_get_contents("php://input");  // 接收原始的xml数据
        $obj = simplexml_load_string($postdata);      // 将原始的xml数据转换成对象

        $ToUserName = $obj->ToUserName;     //公众号的
        $FromUserName = $obj->FromUserName;     //用户的

        if('event' == $obj -> MsgType && 'subscribe' == $obj -> Event){
            $this -> bindSmall($FromUserName);
        }
    }

    /**
     * 绑定小程序
     */
    protected function bindSmall($openId){
        $result = Message::getUnionId($openId);

        if(200 == $result['code']){
            $user = new User();

            $user -> isUpdate(true) -> save(['no_open_id' => $openId], ['unionid' => $result['data']['unionid']]);
        }
    }
}