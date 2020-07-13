<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2018/12/3
 * Time: 11:45
 */

namespace app\api\controller;

use think\Controller;
//use app\index\model\Users;
//use think\facade\Cache;

class Base extends Controller
{
    public $userId = null;

    public function initialize(){
        // 获取头部token
        $token = $this -> request -> header('tokenAccess');
        // 获取接口路径
        $module = $this -> request -> module();
        $controller = $this -> request -> controller();
        $action = $this -> request -> action();
        $route = strtolower($module) . '/' . strtolower($controller) . '/' . strtolower($action);
        // Cache::set( '762bb62002f99ffe279d63ac920afa6f','oSugE5ujXSsr68m4yiJwR2JLEshU', 21600);die;
        // $while = ['index/index/index','index/index/userInfo','index/index/signIn', 'index/orders/notify'];
        // if(!in_array($route, $while)){
        //     if($token){
        //         // 通过token获取openId
        //         $openId = Cache::get($token);
              
        //         if(!$openId){
        //             die(json_encode(echoArr(300, '对不起，打扰了')));
        //         }

        //         $user = new Users();
        //         $info = $user -> where('wx_open_id', $openId) -> field('id,mobile') -> find();

        //         // 判断是否有该用户
        //         if(!$info['id']){
        //             die(json_encode(echoArr(300, '对不起，打扰了')));
        //         }
        //         $this -> userId = $info['id'];
        //     }else{
        //         die(json_encode(echoArr(300, '获取tokenAccess失败')));
        //     }
        // }
    }

    /**
     * 图片上传
     */
    // public function uploadImg($file)
    // {
    //     $info = $file->validate(['size' => 2000000, 'ext' => 'jpg,png,gif'])->move(env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'uploads');
    //     if ($info) {
    //         // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg
    //         return echoArr(200, '上传成功', ['img' => addslashes($info->getSaveName())]);
    //     } else {
    //         // 上传失败获取错误信息
    //         return echoArr(500, $file->getError());
    //     }
    // }
  
    protected function returnData($code, $msg, $data = []){
        return json(echoArr($code, $msg, $data));
    }
}