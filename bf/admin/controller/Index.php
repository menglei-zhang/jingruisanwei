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

use app\admin\model\NodeModel;
use app\admin\model\OrderModel;
use app\admin\model\UserModel;


use app\admin\model\GroupModel;
use think\Session;
  
use think\Db;  

class Index extends Base
{
    public function index()
    {
        // 获取权限菜单
        $node = new NodeModel();

        $a = $node->getMenu(session('rule'));

          $wheremessage = [];  
          $userid = Session::get('user_id');  
          
          $user = new UserModel();

          $where = [];

          $flag = $user->getOneUser($userid);   

           // $where['user_id'] = '';

       if($flag['role_id'] == 3 ) {

                $order_id = Db::name('order')->where('user_id', $flag['user_id'])->field('id')->select();

                foreach ($order_id as $key => $value) {
                     
                    $order_id_sn[$key] = $value['id'];

                 } 

                 $order_id_list = implode(',', $order_id_sn);



                 $wheremessage['order_id'] = array('in',$order_id_list);

         }

         if($flag['role_id'] == 4 ){

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

                      foreach ($order_id as $key => $value) {
                     
                         $order_id_sn[$key] = $value['id'];

                      } 



                       $order_id_list = implode(',', $order_id_sn);

                        $where['user_id'] =  array('in',$userid);

                        $wheremessage['order_id'] = array('in',$order_id_list);  

                        $where['is_view'] = 0;

            }



             if($flag['role_id'] == 6 || $flag['role_id'] == 8 || $flag['role_id'] == 10){


                $a = array_null($flag['order_id']);

                  // var_dump($a);

                $where['id'] = array('in',$a);

                $wheremessage['order_id'] = array('in',$a);

                $where['is_view'] = 2;

         
             }

             // 手工
             if ($flag['role_id'] == 7 || $flag['role_id'] == 9 || $flag['role_id'] == 11) {

                 $a = array_null($flag['order_id']);


                $where['id'] = array('in',$a);

                $wheremessage['order_id'] = array('in',$a);
                  
                 $where['is_view'] = 4;

                // $where['static'] = '4';

             }
        
            
          $viewopen['order'] = Db::name('order')->where($where)->count();  
         



         //订单消息留言



            if($flag['role_id'] == 3 ){
                
                $viewopen['order'] = 0;     
        }
              // 游离于权限外的角色 
             // 部长
        
        if($flag['role_id'] == 5  ){

                    
                    $where['is_minister'] = '0';
                    
                  $viewopen['order'] = Db::name('order')->where($where)->count();  
                                            
            }

            // 总经理
         if($flag['role_id'] == 16  ){

                  
         
                  $where['is_boss'] = '0';

                  $viewopen['order'] = Db::name('order')->where($where)->count();  
                  
                                             
            }

            //财务 
         if($flag['role_id'] == 13  ){

                  
                    $where['is_finance'] = '0';

                  $viewopen['order'] = Db::name('order')->where($where)->count();  
                  
                                             
            }

          
      //订单消息留言

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

        $viewopen['message'] = Db::name('message')->where($wheremessage)->count();     
            
        $viewopen['add'] = $viewopen['order'] + $viewopen['message'];
        
            
    

         $this->assign('viewopen',$viewopen);

        $this->assign([
            'menu' => $node->getMenu(session('rule'))
        ]);



        return $this->fetch('/index');
    }

    /**
     * 后台默认首页
     * @return mixed
     */
    public function indexPage()
    {   


        $order = new OrderModel();
         
        // 今日订单数

        $addtime = date("Y-m-d");

        $where['addtime'] = ['like', '%' . $addtime . '%'];


        $count['addtime'] = $order->getAllorder($where);

        // 总订单数

        $count['allorder'] = $order->getAllorder('1 = 1'); 

        // 进行中

         $count['alling'] = $order->getAllorder('static != 5');

         // 结束
         $count['allend'] = $order->getAllorder('static = 5');

         // 异常
         $count['debuff'] = $order->getAllorder('debuff = 2');
             
         
         $month = date('m');

         $year = date('Y');


        $line['allorder']  = $order->dataorder($month,$year,'1 = 1');

        $line['allend'] = $order->dataorder($month,$year,'static = 5');
    
        $line['alling'] = $order->dataorder($month,$year,'static != 5');

        $line['debuff'] = $order->dataorder($month,$year,'debuff = 2');
            
        $line['allorder']  = implode(',',$line['allorder']);

        $line['allend']  = implode(',',$line['allend']);
        
        $line['alling']  = implode(',',$line['alling']);

        $line['debuff']  = implode(',',$line['debuff']);

        $user = new UserModel();

        $userid = Session::get('user_id');  
        //  $this->assign('vo',$flag);
        $flag = $user->getOneUser($userid);   

 


        $this->assign([
            'show_data' => json_encode($line),
            'count' => $count
        ]);

        $this->assign('vo',$flag);

      
        
        return $this->fetch('index');
    }
}
