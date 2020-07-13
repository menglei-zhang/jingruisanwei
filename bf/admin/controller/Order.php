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

use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use app\admin\model\CustomerModel;

use think\Session;

use think\Db;

use lib\Upload;

use think\Request;

class Order extends Base
{
     
    public function index()
    {   
        if(request()->isAjax()){

            $param = input('param.');
             // var_dump($param);exit;
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

       

            $where = [];
            if (!empty($param) ) {

                // echo 111;
                $where['order_sn'] = ['like', '%' . $param['searchOrderSn'] . '%'];
                 $where['workname'] = ['like', '%' . $param['searchWorkname'] . '%'];
                $where['static'] = ['like', '%' . $param['searchStatic'] . '%'];
                    
            }
            

             $user_id = Session::get('user_id');  
             
             $user = new UserModel();

             $flag = $user->getOneUser($user_id);    

    // +----------------------------------------------------------------------
         // 订单权限开始
          // 业务员    这个地方可以优化 暂时实现功能
    // +----------------------------------------------------------------------

       
             if($flag['role_id'] == 3 || $flag['role_id'] == 19) {

                $where['user_id'] = $flag['user_id'];
             }

             // 销售部组长  同上
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
                        array_push($a, $flag['user_id']);
                     }



                        $userid = implode(',',$a);
                       
                        $where['user_id'] = array('in',$userid);  

            }

            // 销售部部长
            if($flag['role_id'] == 5 ){

                 $group = new GroupModel(); 

                 $allgroup = $group->getGroupList();

                 $a = getTree(objToArray($allgroup));

                 // var_dump($a);exit;

                 foreach ($a[0]['children'] as $k => $v) {

                    $b[] = $v['id'];

                    foreach ($v['children'] as $key => $value) {



                        array_push($b, $value['id'] );


                    }

                 }


                 $groupid = implode(',',$b);

                 $whereis['group_id'] = array('in',$groupid);

                  $usergroup = $user->getUser($whereis);
                
                  foreach ($usergroup as $kl => $val) {

                      $c[$kl] = $val['user_id'];

                  }

                  $userid = implode(',',$c);
                    
     

                  $where['user_id'] = array('in',$userid);  


                                
            }
 
            // 编程  问题

             if($flag['role_id'] == 6 || $flag['role_id'] == 8 || $flag['role_id'] == 10){



                $a = array_null($flag['order_id']);

                // var_dump($a);exit;

                $where['id'] = array('in',$flag['order_id']);


         
             }

             // 手工
             if ($flag['role_id'] == 7 || $flag['role_id'] == 9 || $flag['role_id'] == 11) {

                 $a = array_null($flag['order_id']);


                $where['id'] = array('in',$flag['order_id']);

                // $where['static'] = '4';

             }



            // 订单权限结束


            $content = new OrderModel();
            

            $selectResult = $content->getorderByWhere($where, $offset, $limit);

            
            if(empty($selectResult)){

                 $error = array('code'=>'1','msg'=>'数据为空');

                return json($error);

            }
                        // $status = config('static');

           $static = config('static');

           $debuff = config('debuff');
            
           $uptime = config('uptime'); 


            foreach ($selectResult as $num => $vo) {
                
                    
                $updatatime = $uptime[$vo['uptime']];

                $vo['update'] = $vo['update'] .' '.$updatatime;

                  $grop_id = Db::name('group')->where('id', $vo['confirm'])->find();   

                if(strtotime($vo['update']) < time() ){
                    
                    if( $vo['static'] != 5){

                                

                   @ Db::name('order')->where('id', $vo['id'])->update(['debuff' => '2']);               

                    

                  @  $selectResult[$num]['static'] = $grop_id['group_name'].'-'.$static[$vo['static']].'-'.$debuff[$vo['debuff']];

                  }else{

                  @   $selectResult[$num]['static'] = $grop_id['group_name'].'-'.$static[$vo['static']].'-'.$debuff[$vo['debuff']];        

                  }

                }else{

                    

                    $selectResult[$num]['static'] = $grop_id['group_name'].'-'.$static[$vo['static']];



                }

        
                 $selectResult[$num]['operate'] = showOperate($this->makeButton($vo['id']));

        
                 $all_order_id[] = $vo['id'];
            }

                        
                           
             $orderidall = implode(',',$all_order_id);

            
            // Db::name('user')->where('user_id', $flag['user_id'])->update(['order_id' => $orderidall]);


            $return['total'] = $content->getAllorder($where);  

            $return['rows'] = $selectResult;


             return json($return);
        }



        return $this->fetch();
    }




    public function orderDel()
    {
        $id = input('param.id');

        $content = new OrderModel();
        $flag = $content->delOrder($id);
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 上传
    public function uploadImg()
    {   

        if(request()->isAjax()){

            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            if($info){
                $src =  '/public/upload' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(0, ['src' => $src], ''));
            }else{
                // 上传失败获取错误信息
                return json(msg(-1, '', $file->getError()));
            }
        }
    }


    //多图上传 
    public function arrayImg()
    {       
        if($this->request->isPost()){
                 $res['code']=1;
                 $res['msg'] = '上传成功！';
                 $file = $this->request->file('file');
                 $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');

                 if($info){

                     $res['filepath'] = '/public/upload' . '/' . date('Ymd') . '/' . $info->getFilename();
                     return json(msg(0, ['src' =>  $res['filepath']], ''));
                 }else{
                     $res['code'] = 0;
                     $res['msg'] = '上传失败！'.$file->getError();
                 }
                 return $res;
            }
    }


        public function bigarray(){

        if($this->request->isPost()){


             @$file = $this->request->file('file');

             @$param = input('param.');

             // var_dump($param);exit;

             // $a = get_object_vars($file);  

             @$a = object_array($file);
            
                

            @$upload = new Upload($a[1],$param['blob_num'],$param['total_blob_num'],$param['file_name'],$param['md5_file_name']);
              
            @$upload_list = $upload->apiReturn();

            // $list = json_decode($upload_list);
                
             return $upload_list;   


        }

    }
    
    public function downloadlist()
    {
        if($this->request->isPost()){

            $param = input('param.');

            // $loadlist =  $param['download'];

            
           $down_host = $param['download']; 

            return $down_host;    
        
        }

    }


    // 添加订单
    public function OrderAdd()
    {
        if(request()->isPost()){

             $user = new UserModel();

            $param['update'] = '';

            $param = input('post.');
            

            $user_id = Session::get('user_id');
            

        
 
            if (!preg_match( '/^[1-9]\d*|^[1-9]\d*.\d+[1-9]$/',$param['place'])) {

                    return json(msg(-1, '', '只能是纯数字'));

            }


           if(strtotime($param['update']) < time() ){   

                    return json(msg(-1, '', '请重新选择时间'));

           }

           

                
             $flag = $user->getOneUser($user_id );    

             if($flag['role_id'] == 4){

                  $user = explode (',', $param['workname']);

                  $param['workname'] = $user[0]; 
                        
                   $param['user_id'] = $user[1];
             }else{

                     $param['user_id'] = $user_id; 
             }

     


             // 跟接口数据统一不然会导致接口报错



             if(!empty($param['pc_src'])){

                          foreach ($param['pc_src'] as $key => $value) {
                        
                     $request = Request::instance();

                     $domain = $request->domain();
   
                     $param_list[$key] = $value;

              }

            $param['pc_src'] = $param_list;

      

             $param['pc_src'] = implode(',', $param['pc_src']);
              
         }                     



             // $src
  
            unset($param['file']);

            $param['addtime'] = date('Y-m-d H:i:s');

            $content = new OrderModel();



             // var_dump($param);exit;

            $flag = $content->addorder($param);

            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }


         $role_id = Session::get('role_id');  

         // 查出指定用户的权限

         $group = new GroupModel();

         $user = new UserModel();

         $userid = Session::get('user_id');  
        //  $this->assign('vo',$flag);
         $flag = $user->getOneUser($userid);   


    
         // 查出所有客户的名字

         $customer = new CustomerModel();

         // $name = $customer->where('user_id',$flag['user_id'])->getCustomer('cust_name'); 

         $query = array();

         $name = array();

         if($flag['role_id'] == 3 ){

                 $name = Db::name('customer')->where('user_id',$flag['user_id'])->select();  

         } 

        

         if($flag['role_id'] == 4){

                $children = $group->getgrouppeople($flag['group_id']);

               foreach ($children as $key => $value) {
                  
                   $a['user_id'][$key] = $value['user_id'];

                   array_push($a['user_id'], $flag['user_id']);
                   
               }

                        
            $flag['user_id'] = implode(',', $a['user_id']);

            // var_dump($flag['user_id']);

            $name = Db::name('customer')->where('user_id','in',$flag['user_id'])->select();  

            // $user_id = $flag['user_id'];

            $query = Db::name('user')->where('user_id','in',$flag['user_id'])->select();  

         }



         $shenchan = Db::name('group')->where('type_id','8')->select();

         // var_dump($shenchan);

         $this->assign('shenchan',$shenchan);

         $this->assign('real',$query);

         $this->assign('vo',$flag);

         $this->assign('name',$name);

        return $this->fetch();
    }



    // 修改订单
        public function orderEdit()
    {
        $content = new OrderModel();

        if(request()->isPost() ){

            $content = new OrderModel();

            $user = new UserModel();

            $param = input('post.');

            

              $param['pc_src'] = implode(',', $param['pc_src']);

            // unset($param['file']);
            @$confirm = $param['confirm'];
            

            
           // +----------------------------------------------------------------------
                    //     如果生产部选择换成多选     换成这个   
           //  foreach ($confirm as $key => $value) {

           //       $query[] = Db::table('snake_role')
           //      ->where('role_name','like',"%$value[$key]%")
           //      ->select();

                
           //  }

           // foreach ($query as $k => $v) {
                               
           //       foreach ($v as $a => $j) {
           //                 $data['id'][] =  $j['id'];
           //                 $data['role_name'][] =  $j['role_name'];
           //           }            
           //  } 

           //  $userlist = Db::table('snake_user')->where('role_id','in',$data['id'])->field('user_id')->select();
  
           //  foreach ($userlist as $key => $value) {
                
           //      $sc_id =  Db::table('snake_user')->where('role_id',$data['user_id'])->field('user_id')->select();

           //  }

             // +----------------------------------------------------------------------

         // 组长提交到编程逻辑开始
          if($param['role_id'] == '4' ){

              $orderstatic = Db::name('order')->where('id',$param['id'])->find();
              
                
              if($orderstatic['static'] == 2){



                $error = array('code'=>'-1','msg'=>'订单重复提交');

                return json($error);


                exit;
              }



             $query = Db::name('group')->where('type_id',$param['confirm'])->select();

              $i = 0;
                    
                foreach ($query as $k => $v) {
                                     
                    
                    $i++;


                   $datas['id'][] =  $v['id'];
                    

                  $userlist = Db::table('snake_user')->where('group_id',$datas['id'][0])->order('group_id','desc')->select();  
                   


             } 

                   
                
                      
                    $count = count($userlist);


                  //2.然后在数据库生成每次提交符合条件的订单就会同步增加数字

                    foreach ($userlist as $k => $v) {
                                             



                         // $v['orderby'] = $count + 1;
                                                      
                        $userlist[$k]['orderby'] = $v['orderby'] + 1;

                        $b[] = $userlist[$v['orderby'] - 1]['order_id'];

                        $userlist[$v['orderby'] - 1]['order_id'] =  $param['id'];
                        
                    // 判斷時候否為空，如果不為空，那麼將數據拼接起來



                        if(!$userlist[$k]['order_id'] == null){

                             // var_dump( $b[0]) ;  
                             $userlist[$v['orderby'] - 1]['order_id'] =  $b[0];

                            $userlist[$v['orderby'] - 1]['order_id'] .= ','.$param['id'];

                            // var_dump($a);
                        }

                //3. 增加到等於可執行的最大數量時  將其賦值為1

                    if($userlist[$k]['orderby'] > $count){

                             $userlist[$k]['orderby'] = 1;
                      

                    }

                        

                         $param['static'] = 2; 
                             

                }  


                    // var_dump($userlist);exit;
                   
                     $user = new UserModel();

                    // 修改订单进程状态和消息提示状态.

                     $result = Db::name('order')->where('id', $param['id'])->update(['static' => '2','is_view'=>'2']);




                 
                    $user->updateall($userlist); 

                     // $list = array('msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  

                     // return json($list);  


             }


             // 生产部编辑提交给手工

            if($param['role_id'] == '6' || $param['role_id'] == '8' || $param['role_id'] == '10'){  


                $query = Db::name('group')->where('type_id',$param['confirm'])->select();


               $i = 0;
               
               $userlist = '';

                foreach ($query as $k => $v) {
                
                    
                    $i++;

                    // echo $i.'次循环';


                   $data['id'][] =  $v['id'];
                    
                   $userlist = Db::table('snake_user')->where('group_id',$data['id'][$i-1])->order('group_id','desc')->select();  
                   


             } 

              $orderstatic = Db::name('order')->where('id',$param['id'])->find();

             // if($orderstatic['static'] == 4){

             //    $error = array('code'=>'-1','msg'=>'订单重复提交');

             //    return json($error);


             //    exit;

             // }


                $count = count($userlist);


              //2.然后在数据库生成每次提交符合条件的订单就会同步增加数字

                foreach ($userlist as $k => $v) {
                                         



                     // $v['orderby'] = $count + 1;
                                                  
                    $userlist[$k]['orderby'] = $v['orderby'] + 1;

                    $b[] = $userlist[$v['orderby'] - 1]['order_id'];

                    $userlist[$v['orderby'] - 1]['order_id'] =  $param['id'];
                    
                // 判斷時候否為空，如果不為空，那麼將數據拼接起來



                    if(!empty($userlist[$k]['order_id'])){

                         // var_dump( $b[0]) ;  
                         $userlist[$v['orderby'] - 1]['order_id'] =  $b[0];

                        $userlist[$v['orderby'] - 1]['order_id'] .= ','.$param['id'];

                         // var_dump($userlist);
                    }

            //3. 增加到等於可執行的最大數量時  將其賦值為1

                if($userlist[$k]['orderby'] > $count){

                         $userlist[$k]['orderby'] = 1;
                  

                }

                    
            }  


                    $user = new UserModel();

                    // var_dump($userlist);exit;

                    $user->updateall($userlist); 


              

            }



            
        // 驳回订单 

         if($param['role_id'] == 3 || $param['static'] == 3){

                 $param['static'] = 1; 

         }



           $flag = $content->editorder($param);

           
           return json(msg($flag['code'], $flag['data'], $flag['msg']));


        }
        

         $data = input('param.');
        

          $userid = Session::get('user_id');  

         $user = new UserModel();

         $group = new GroupModel();

         $flag = $user->getOneUser($userid);   

         // var_dump($flag['role_id']);

         // $data['is_view'] = 1;
        // 修改消息通知状态
          if($flag['role_id'] === 4){

                $data['is_view'] = 1;
                  $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);
          }     

          if($flag['role_id'] === 6 || $flag['role_id'] === 8 || $flag['role_id'] === 10){

                $data['is_view'] = 3;
                   $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);
          }
         

          if($flag['role_id'] === 7 || $flag['role_id'] === 9 || $flag['role_id'] === 11){

                $data['is_view'] = 5;
                   $list = Db::table('snake_order')->where('id', $data['id'])->update(['is_view' => $data['is_view']]);
          }

              // 游离于权限逻辑外的角色 
          // 部长


        if($flag['role_id'] === '5' ){

                       
                // $data['is_out_role'] = '1';
                    
                  Db::name('order')->where('id', $data['id'])->update(['is_minister' => $data['is_out_role']]);
                                            
            } elseif($flag['role_id'] === '16'){

            // 总经理
            

                  // $data['is_out_role'] = '1';

                  Db::name('order')->where('id', $data['id'])->update(['is_boss' => $data['is_out_role']]);
                                             
            }   elseif($flag['role_id'] === '13 ' ){

            //财务 
      

                  
                    // $data['is_out_role'] = '1';
                 Db::name('order')->where('id', $data['id'])->update(['is_finance' => $data['is_out_role']]);
                  
                                             
            }

         $static = $content->getOneorder($data['id']);        

         $userid = Session::get('user_id');  

         $user = new UserModel();

         $flag = $user->getOneUser($userid);    

         $flag['static'] = $static['static'];    

        // ----------------
         // 留言消息通知
         // ---------------

        $message_zt = array();



        // 业务员
        if($flag['role_id'] == '3'){

            $message_zt['is_view_message'] = 1;
        }
        // 组长
        if($flag['role_id'] == '4'){

            $message_zt['is_minister'] = 1;

        }
        // 部长
        if($flag['role_id'] == '5'){

            $message_zt['is_bz'] = 1;

        }
        // sla编程
        if($flag['role_id'] == '6'){

            $message_zt['is_3d_printing'] = 1;
        }
        // 后处理
        if($flag['role_id'] == '7'){

            $message_zt['is_3d_sg'] = 1;
        }
        // cnc
        if($flag['role_id'] == '8'){

            $message_zt['is_cnc'] = 1;
        }
        // cnc手工
        if($flag['role_id'] == '9'){

            $message_zt['is_cnc_sg'] = 1;
        }
        // 复模
        if($flag['role_id'] == '10'){

            $message_zt['is_fumo'] = 1;
        }
        // 复模手工
        if($flag['role_id'] == '11'){

            $message_zt['is_fumo_sg'] = 1;
        }
        // 财务
        if($flag['role_id'] == '13'){

            $message_zt['is_finance'] = 1;

        }
        // 总经理
        if($flag['role_id'] == '16'){

            $message_zt['is_boss'] = 1;

        }

        $list = Db::name('message')->where('order_id', $data['id'])->update($message_zt);
        

         // 查对应订单所有留言

          $message = Db::name('message')->where('order_id',$data['id'])->order('add_time','desc')->select(); 


       foreach ($message as $k => $v) {
                
            $message[$k]['mess_img'] = explode(',',$v['mess_img']);
            
         }  


          // *客户名称：
         $name = Db::name('customer')->where('user_id',$flag['user_id'])->select();   

        $customer = new CustomerModel();




          if($flag['role_id'] == 3 ){

             $name = Db::name('customer')->where('user_id',$flag['user_id'])->select();  

         } 

        

         if($flag['role_id'] == 4){

                $children = $group->getgrouppeople($flag['group_id']);

                   foreach ($children as $key => $value) {
                  
                   $a['user_id'][$key] = $value['user_id'];

                   array_push($a['user_id'], $flag['user_id']);
                   
               }

                        
            $flag['user_id'] = implode(',', $a['user_id']);

            // var_dump($flag['user_id']);

            $name = Db::name('customer')->where('user_id','in',$flag['user_id'])->select();  

            // $user_id = $flag['user_id'];

            $query = Db::name('user')->where('user_id','in',$flag['user_id'])->select();  

         }

       
          $this->assign('message',$message);

         // 当前登录用户信息
         $this->assign('vo',$flag);

         $orderlist = $content->getOneorder($data['id']);    

         if(empty($orderlist)){

              $this->error('订单号有误请联系管理人员','view/index');

                exit;
         }

    
        

         // 判断是否超时

         $uptime = config('uptime'); 

        $updatatime = $uptime[$orderlist['uptime']];

        $orderlist['update'] = $orderlist['update'] .' '.$updatatime;

        $orderlist['overtime'] = '未超出';


        if(strtotime($orderlist['update']) <= time() ){

            if($orderlist['static'] != 5){

                $overtime = time() - strtotime($orderlist['update']); 

                $minute = $overtime / 60;         //60秒1分钟
 
                $hour = $minute / 60;     //60分钟1小时

                $over = round($hour);



                $orderlist['overtime'] = $over;
            }
        }

                  
        $shenchan = Db::name('group')->where('type_id','8')->select();


         $this->assign('shenchan',$shenchan);

         $this->assign('content',$orderlist);

         $this->assign('name',$name);

        return $this->fetch();
    }


     //修改订单状态 
        public function editstatic()
        {
            if(request()->isPost()){

                // $res['code']=1;
                // $res['msg'] = '上传成功！';

                $param = input('post.');

                $list = Db::table('snake_order')->where('id', $param['id'])->update(['static' => $param['static']]);
                     
                return $list;
               

            }
            
        }

     

    //添加留言信息

        public function addmessage()
        {
            if(request()->isPost()){

                $param = input('post.');

                $request = Request::instance();

                $domain = $request->domain();

                
                // var_dump($param);

                 $result = db('message')->insert($param);   

            } 



        }   

    // 删除留言信息

        public function delmessage()
        {
            if(request()->isPost()){

                $param = input('post.mess_id');


                 $result = db('message')->where('mess_id',$param)->delete();

            } 
        }



    /**
     * 拼装操作按钮
     * @param $id
     * @return array
     */
    private function makeButton($id)
    {   
        return [
            '删除' => [
                'auth' => 'order/orderdel',
                'href' => "javascript:orderDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ],
            '编辑' => [
                'auth' => 'order/orderedit',
                'href' => url('order/orderEdit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],

        ];
    }






}
