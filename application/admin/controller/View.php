<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use app\admin\model\ViewModel;
  
use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use app\admin\model\CustomerModel; 

use think\Session;
  
use think\Db;  
  

class View extends Base
{
    // 消息列表
    public function index()
    {
           $userid = Session::get('user_id');  
           $user = new UserModel();
           $flag = $user->getOneUser($userid);   
           $upload_view = '';
      	   $where = [];
           $wheremessage = [];  
       
           if($flag['role_id'] == 3 ) {

               $order_id = Db::name('order')->where('user_id', $flag['user_id'])->field('id')->select();

                foreach ($order_id as $key => $value) { 
                     
                    $order_id_sn[$key] = $value['id'];

                 } 

                 $order_id_list = implode(',', $order_id_sn);
				 //业务员确认消息通知
                $where['user_id'] = $flag['user_id'];  

                $where['is_view'] = '5';

                $where['static'] = '5';			
             	 
                 $wheremessage['order_id'] = array('in',$order_id_list);

             }


             if($flag['role_id'] == 4  ){

                 $whereis['role_id'] = $flag['role_id'];
                 $whereis['user_id'] = $flag['user_id'];
                 $usergroup = $user->getUserfind($whereis);
                    
                     $group = new GroupModel();               

                     // var_dump($usergroup['group_id']);exit;

                     $user = $group->getgrouppeople($usergroup['group_id']);  

                     // 将查到的多个userid组合成字符串

                     $a = array();

                     foreach ($user as $key => $v) {
                        $a[$key] = $v['user_id'];
                           
                     }

                        $userid = implode(',',$a);
                       
                        $order_id = Db::name('order')->where('user_id','in',$userid)->field('id')->select();

               			$order_id_sn = [];
                      foreach ($order_id as $key => $value) {
                     
                         $order_id_sn[$key] = $value['id'];

                      } 
							
                       $order_id_list = implode(',', $order_id_sn);

                       $where['user_id'] =  array('in',$userid);

                       $wheremessage['order_id'] = array('in',$order_id_list);  

                       $where['is_view'] = 0;

                       $upload_view = 1;


            }

              // 编辑
            
             if($flag['role_id'] == 6 || $flag['role_id'] == 8 || $flag['role_id'] == 10){

                $a = array_null($flag['order_id']);

                // var_dump($a);

                $where['id'] = array('in',$a);

                $wheremessage['order_id'] = array('in',$a);

                $where['is_view'] = 2;

                $upload_view = 3;
         
             }

             // 手工
             if ($flag['role_id'] == 7 || $flag['role_id'] == 9 || $flag['role_id'] == 11) {

                 $a = array_null($flag['order_id']);

                $where['id'] = array('in',$a);

                $wheremessage['order_id'] = array('in',$a);

                $where['is_view'] = 4;

                $upload_view = 5;
                // $where['static'] = '4';

             }


              $order_id = Db::name('order')->where($where)->field('id')->select();
              
              // var_dump($order_id);

              $order_id_info = '';

              $order_id_info_all = '';

              foreach ($order_id as $key => $v) {
                  
                 $order_id_info[$key] =  $v['id'];

              }
                

           $viewopen['order'] = Db::name('order')->where($where)->order('addtime','desc')->select();

           // var_dump($viewopen);

            //订单消息留言

         // if($flag['role_id'] == 3 ){    
             //   $viewopen['order'] = '';     
        // }


         // 游离于权限逻辑外的角色 
             // 部长
           if($flag['role_id'] == 5  ){
     
             	$where['is_minister'] = '0';

             	$viewopen['order'] = Db::name('order')->where($where)->order('addtime','desc')->select();
                                             
            }

            // 总经理
         if($flag['role_id'] == 16  ){

                  $where['is_boss'] = '0';

                  $viewopen['order'] = Db::name('order')->where($where)->order('addtime','desc')->select();
                  
                                             
            }

            //财务 
         if($flag['role_id'] == 13  ){

                  
                    $where['is_finance'] = '0';

                   $viewopen['order'] = Db::name('order')->where($where)->order('addtime','desc')->select();
                  
                  
                                             
            }


            // ----------------
           // 留言消息通知
           // ---------------
            
            $message_zt = array();
            // 业务员
            if($flag['role_id'] == '3'){

                 $wheremessage['order_id'] = array('in',$order_id_list);

                $wheremessage['is_view_message'] = 0;
            }
            // 组长
            if($flag['role_id'] == '4'){

                $wheremessage['order_id'] = array('in',$order_id_list);  

                $wheremessage['is_minister'] = 0;

            }
            // 部长
            if($flag['role_id'] == '5'){

                $wheremessage['is_bz'] = 0;

            }
            // sla编程
            if($flag['role_id'] == '6'){

                $a = array_null($flag['order_id']);

                $wheremessage['order_id'] = array('in',$a);

                $wheremessage['is_3d_printing'] = 0;
            }
            // 后处理
            if($flag['role_id'] == '7'){

                $a = array_null($flag['order_id']);

                $wheremessage['order_id'] = array('in',$a);

                $wheremessage['is_3d_sg'] = 0;
            }
            // cnc
            if($flag['role_id'] == '8'){

                $a = array_null($flag['order_id']);

                $wheremessage['order_id'] = array('in',$a);

                $wheremessage['is_cnc'] = 0;
            }
            // cnc手工
            if($flag['role_id'] == '9'){

                $a = array_null($flag['order_id']);

                $wheremessage['order_id'] = array('in',$a);

                $wheremessage['is_cnc_sg'] = 0;
            }
            // 复模
            if($flag['role_id'] == '10'){

                $a = array_null($flag['order_id']);

                $wheremessage['order_id'] = array('in',$a);

                $wheremessage['is_fumo'] = 0;
            }
            // 复模手工
            if($flag['role_id'] == '11'){

                $a = array_null($flag['order_id']);

                $wheremessage['order_id'] = array('in',$a);
                
                $wheremessage['is_fumo_sg'] = 0;
            }
            // 财务
            if($flag['role_id'] == '13'){

                $wheremessage['is_finance'] = 0;

            }
            // 总经理
          if($flag['role_id'] == '16'){

            $wheremessage['is_boss'] = 0;

        }

             $viewopen['message'] = Db::name('message')->where($wheremessage)->select();


             $this->assign('viewopen',$viewopen);

             $this->assign('upload_view',$upload_view);
             
             $this->assign('flag',$flag);
             
            return $this->fetch();

    }

    

    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id)
    { 
        return [
            '编辑' => [
                'auth' => 'articles/articleedit',
                'href' => url('articles/articleedit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],

        ];
    }
}
