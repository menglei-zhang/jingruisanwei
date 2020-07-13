<?php
namespace app\api\controller;

use think\Db;
use think\Session;
use think\Cookie;
use think\Request;
use app\admin\model\OrderModel;
use app\admin\model\UserModel;
class Login extends Base
{

  #用户登陆#

  //用户名 username
  //密码 password
  //小程序秘钥  code



   public function login(){
    
    if($this->request->isPost()){
            $code = config('code');

             $msg = config('msg');

            $data = input('post.');



             $request = Request::instance();

              $domain = $request->domain();


           if(count($data) == 1){
                  
                $static = config('wx');
                                
                $appid = $static['appid'];

     

                $secret = $static['secret'];
              // $code=$_GPC['code'];     //微擎获取前台上传的code值
                 $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$data['code'].'&grant_type=authorization_code';


                 $infos = file_get_contents($url);//get请求网址，获取数据
                  
                 $json = json_decode($infos);//对json数据解码

                  $arr = get_object_vars($json);//返回一个数组。获取$json对象中的属性，组成一个数组

                  // var_dump($arr);

                  if(@!empty($arr['errcode'])){
                    

                       $error = array('data'=>$arr,'code'=>$code['AccessTokenError'],'msg'=>$msg['AccessTokenError']);

                       return  json($error);

                       exit;
                  }

                  $openid = $arr['openid'];

                  $info= Db::name('user')->where('open_id',$openid)->find();

                   if(empty($info)){

                        // return apiReturn( 'ParamEmpty');
                     $list = array('msg'=>$msg['UserNotLogged'],'code'=>$code['UserNotLogged']);
                     return json($list); 

                      exit;
                   } 

                  Session::set('wx_user',$info);

                   $list = array('data'=>$info,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);


                    return json($list); 

                    exit;  
            }  


              // 名称
            if(empty($data['username'])){
               return json(array('code'=>$code['ParamError'], 'msg'=>'用户名为空'));
               exit;
            }

            // 密码
            if(empty($data['password'])){
                return json(array('code'=>$code['ParamError'], 'msg'=>'密码为空'));
                exit;
            }
              if(empty($data['code'])){
                return json(array('code'=>$code['ParamError'], 'msg'=>'秘钥为空'));
                exit;
            }


              $map=array(
            'user_name'=>$data['username'],
            'password'=>md5($data['password'] . config('salt')),
            'status'=>'1',
          );

              

             $static = config('wx');
              
              
              $appid = $static['appid'];

              $secret = $static['secret'];
              // $code=$_GPC['code'];     //微擎获取前台上传的code值
               $url = 'https://api.weixin.qq.com/sns/jscode2session?appid='.$appid.'&secret='.$secret.'&js_code='.$data['code'].'&grant_type=authorization_code';

               
              // var_dump($url);

               $infos = file_get_contents($url);//get请求网址，获取数据
                
               $json = json_decode($infos);//对json数据解码

               $arr = get_object_vars($json);//返回一个数组。获取$json对象中的属性，组成一个数组


               if(empty($arr['openid'])){

                    $error = array('code'=>$code['ParamError'],  'msg'=>'数据为空');

                     return json($error);
                     exit;
               }

               $openid = $arr['openid'];


               $session_key = $arr['session_key'];
                            

               $info = Db::name('user')->where('user_name',$data['username'])->update(['open_id' => $openid]);



               $list = Db::name('user')->where('user_name',$data['username'])->find();


                $list['head'] = $domain.$list['head'];

                Session::set('wx_user',$info);

                // json_encode(value)

                $lists = array('data'=>$list,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);

                  
               return json($lists); 

       
            
        }else{
            return  json(echoArr(500, '非法请求'));
        }


    }
    
    // 修改密码
    // new_password  新密码
    // re_new_password  重复新密码
    // password  原密码
    // user_id 
    public function updatePassword()
    {

        
         $code = config('code');

         $msg = config('msg');

       

        //提交修改
        if ($this->request->isPost()) {

            $data = input('post.');

            if (empty($data)) {
                return json(array('code'=>$code['ParamEmpty'],'msg'=>'参数为空'));
              
            }

            if ($data['new_password'] !== $data['re_new_password']) {
                return json(array('code'=>$code['ParamError'],'msg'=>'2次新密码输入不相同'));
            }

            $user_model = new UserModel();

            // $wx_user = session('wx_user');

             // $user_data = $user_model->getOneUser();

             $user_data = $user_model->getOneUser($data['user_id']);

             // exit;

            if (is_null($user_data)) {
                return json(array('code'=>$code['ParamEmpty'],'msg'=>'用户不存在'));
            }

            if ($user_data['password'] !== md5($data['password']. config('salt'))) {
                return json(array('code'=>$code['ParamError'], 'msg'=>'原始密码错误'));
            }

            if ($user_data['password'] === md5($data['new_password']. config('salt'))) {
                return json(array('code'=>$code['ParamError'], 'msg'=>'新密码不能和旧密码相同'));
            }

             $param['password'] = md5($data['new_password']. config('salt'));

             $flag = $user_model->updateStatus($param, $data['user_id']);

                $list = array('data'=>'','msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);
                
           
               return json($list); 
        }

        
    }


    
}
