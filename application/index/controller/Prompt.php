<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/7/1
 * Time: 17:33
 */

namespace app\index\controller;


use think\Controller;
use app\admin\model\NoticeModel;

class Prompt extends Controller
{
    /**
     * 是否有新留言
     */
    public function response(){
        if(!Session('user_id')){
            return json(echoArr(500, '请求失败'));
        }

        $data = input('post.');
        $data['user_id'] = Session('user_id');

        // 未读留言
        $notice = new NoticeModel();
        $info = $notice -> where('user_id', $data['user_id']) -> where('is_have_read', 0) -> select();
      	
        // 提示音
        $prompt = 0;
        $temp = [
            'order' => [],
            'message' => [],
        ];
        $count = 0;
        foreach($info as $v){
            if($v['add_time']) $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);

            if($v['type'] == 0){
                $temp['order'][] = $v;
            } else {
                $temp['message'][] = $v;
            }

            if(0 == $prompt && 0 == $v['is_prompt']) {
                $prompt = 1;
            }

            $count++;
        }

        $returnData = [
            'info' => $temp,
            'prompt' => $prompt,
            'count' => $count
        ];

        return json(echoArr(200, '请求成功', $returnData));
    }

    /**
     * 提示音数据处理
     */
    public function handle(){
        if(!Session('user_id')){
            return json(echoArr(500, '请求失败'));
        }

        $data = input('post.');
        $data['user_id'] = Session('user_id');

        $notice = new NoticeModel();
        $result = $notice -> where('user_id', $data['user_id']) -> update(['is_prompt' => 1]);

        if(false === $result){
            return json(echoArr(500, '请求失败'));
        }else {
            return json(echoArr(200, '请求成功'));
        }
    }

    /**
     * 消息已读
     */
    public function infoHaveRead(){
        if(!Session('user_id')){
            return json(echoArr(500, '请求失败'));
        }

        $data = input('post.');
        $data['user_id'] = Session('user_id');

        if(!isset($data['notice_id']) || !$data['notice_id']){
            return json(echoArr(500, '请求失败'));
        }

        $notice = new NoticeModel();
        $result = $notice -> where('user_id', $data['user_id']) -> where('id', $data['notice_id']) -> update(['is_have_read' => 1]);

        if(false === $result){
            return json(echoArr(500, '请求失败'));
        }else {
            return json(echoArr(200, '请求成功'));
        }
    }
}