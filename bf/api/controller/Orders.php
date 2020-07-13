<?php


namespace app\api\controller;

use think\Db;

use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use app\admin\model\CustomerModel;

use think\Request;

class Orders extends Base
{
    /**
     * 订单列表 
     * user_id    用户ID    
     * role_id   权限ID
     */
    public function orderList()
    {

        if($this->request->isPost()){




            $code = config('code');

             $msg = config('msg');

            $data = input('post.');
                
            if(empty($data['user_id']) ){
               
                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'user_id为空');

                 return  json($error);

                 exit;
            }   



             $datas =  Db::name('user')->where('user_id',$data['user_id'])->find(); 


            $where = [];


            $user = new UserModel();


            if($datas['role_id'] == 3 || $datas['role_id'] == 19) {

                $where['user_id'] = $data['user_id'];
             }

             // 销售部组长  同上
            if($datas['role_id'] == 4 ){

                 $whereis['role_id'] = $datas['role_id'];
                 $whereis['user_id'] = $data['user_id'];
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
                       
                        $where['user_id'] = array('in',$userid);  

            }

            // 销售部部长
            if($datas['role_id'] == 5 ){

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


                        // 编程

             if($datas['role_id'] == 6 || $datas['role_id'] == 8 || $datas['role_id'] == 10){


                $order_id = explode(',',$datas['order_id']);  


                                  
                array_filter($order_id);

                $where['id'] = array('in',$order_id);


                 
             }

             // 手工
             if ($datas['role_id'] == 7 || $datas['role_id'] == 9 || $datas['role_id'] == 11) {
                




               $order_id = explode(',',$datas['order_id']); 



                array_filter($order_id);


                $where['id'] = array('in',$order_id);

                // $where['static'] = '4';


             }

             $order = array();

             $order = Db::name('order')->where($where)->select(); 
            
              if(empty($order) ){

                 $error = array('data'=>$order,'code'=>$code['RequestSuccess'],'msg'=>'该用户订单为空');

                 return  json($error);

                 exit;
            }     

              // var_dump($order);exit;
            foreach ($order as $k => $v) {
                 
                $orders[$k]['id'] = $v['id'];

                // var_dump($orders[$k]['id']);

                @$orders[$k]['username'] = $v['username'];
                @$orders[$k]['order_sn'] = $v['order_sn'];
                @$orders[$k]['workname'] = $v['workname'];

                @$grop_id = Db::name('group')->where('id', $v['confirm'])->find();
                
                 // $orders[$k]['confirm'] = $grop_id['group_name'];

                $static = config('static');

                $debuff = config('debuff');

                @$orders[$k]['confirm'] = $grop_id['group_name'].'-'.$static[$v['static']].'-'.$debuff[$v['debuff']];

                 @$orders[$k]['static'] =  $v['static'];

                 @$orders[$k]['debuff'] =  $v['debuff'];

                @$uptime = config('uptime'); 
                @$updatatime = $uptime[$v['uptime']];
                @$orders[$k]['update'] = $v['update'] .' '.$updatatime;
            }     

            $list = array('data'=>$orders,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);
           
            return json($list); 
               
            exit;
          
        }else{

            return  json(echoArr(500, '非法请求'));

        }
    
    }
    // 订单详情

    // order_id   订单id
    // user_id   用户id
    public function orderDetails()
    {

         if($this->request->isPost()){

                $code = config('code');

             $msg = config('msg');

            $data = input('post.');

            if(empty( $data['order_id']) ){

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'订单id为空');

                 return  json($error);

                 exit;
            }   

               if(empty($data['user_id']) ){
               
                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'用户ID为空');

                 return  json($error);

                 exit;
            }  
             $orderlist = Db::name('order')->where('id',$data['order_id'])->find();

             if($orderlist == null ){

                $error = array('code'=>$code['ParamEmpty'] , '查询数据为空或订单ID错误');

                 return  json($error);

                 exit;
            }   



            $message = Db::name('message')->where('order_id',$data['order_id'])->select();

            if(!empty($orderlist['pc_src'])){

            $imgs = explode(',',$orderlist['pc_src']);

            $i = 0;

            foreach ($imgs as $key => $value) {
                $i++;
                if($i == 1){

                    $imgs = $imgs;
                }else{

                     array_filter($imgs);

                }

            }

           

            

             $request = Request::instance();

             $domain = $request->domain();
        
            foreach ($imgs as $k => $v) {
                    
                    $imgs[$k] = $v;
                    // $imgs[$k]['imgname'] = 
                }

            }else{

                $imgs = array();
            }

              $orderdetails = array();

               $orderdetails['machine_number'] = '';

               $orderdetails['date'] = '';
               
               $orderdetails['file_name'] = '';


            if(empty($message)){
                    $static = config('static');
                    $debuff = config('debuff');
                    
                    $grop_id = Db::name('group')->where('id', $orderlist['confirm'])->find();

                // $orders[$k]['confirm'] = $grop_id['group_name'];

                    $static = config('static');

                    $debuff = config('debuff');

                    @ $orderdetails['static'] = $grop_id['group_name'].'-'.$static[$orderlist['static']].'-'.$debuff[$orderlist['debuff']];


                     if($orderlist['static'] == 2){

                         $orderdetails['staticnum'] = 1 ;       
                     }elseif($orderlist['static'] == 3){

                        $orderdetails['staticnum'] = 2  ;

                     }else{

                        $orderdetails['staticnum'] = 0  ;

                     }

                    $orderdetails['order_id'] = $orderlist['id'];
                   @ $orderdetails['pc_src'] = $imgs;
                    $orderdetails['username'] = $orderlist['username'];
                    $orderdetails['workname'] = $orderlist['workname'];


                    $orderdetails['confirm'] = $grop_id['group_name'];

                    $orderdetails['confirm_num'] = $orderlist['confirm'];

                    $orderdetails['craft'] = $orderlist['craft'];
                    $orderdetails['order_sn'] = $orderlist['order_sn'];
                    $orderdetails['place'] = $orderlist['place'];

                    $uptime = config('uptime'); 
                    $updatatime = $uptime[$orderlist['uptime']];
                    $orderdetails['update'] = $orderlist['update'] .' '.$updatatime;

                    $orderdetails['sanding'] = $orderlist['sanding'];
                    // $orderdetails['file_name'] = $orderlist['file_name'];
                    $orderdetails['num'] = $orderlist['num'];
                    $orderdetails['weight'] = $orderlist['weight'];
                    $orderdetails['material'] = $orderlist['material'];
                    $orderdetails['machine_number'] = $orderlist['machine_number'];
                    $orderdetails['date'] = $orderlist['date'];
                    // $orderdetails['programmer'] = $orderlist['programmer'];
                    $orderdetails['be_careful'] = $orderlist['be_careful'];

                    $orderdetails['message'] = array();
            }else{


            foreach ($message as $k => $v) {

                     $request = Request::instance();

                    $domain = $request->domain();

                    $static = config('static');

                    $debuff = config('debuff');

                    $grop_id = Db::name('group')->where('id', $orderlist['confirm'])->find();

                // $orders[$k]['confirm'] = $grop_id['group_name'];

                    $static = config('static');

                    $debuff = config('debuff');

                    @ $orderdetails['static'] = $grop_id['group_name'].'-'.$static[$orderlist['static']].'-'.$debuff[$orderlist['debuff']];

                    // $orderdetails['static'] = $orderlist['confirm'].'-'.$static[$orderlist['static']].'-'.$debuff[$orderlist['debuff']];

                      if($orderlist['static'] == 2){

                         $orderdetails['staticnum'] = 1 ;       
                     }elseif($orderlist['static'] == 3){

                        $orderdetails['staticnum'] = 2  ;

                     }else{

                        $orderdetails['staticnum'] = 0 ;

                     }

                    $orderdetails['order_id'] = $orderlist['id'];
                   @ $orderdetails['pc_src'] = $imgs;
                    $orderdetails['username'] = $orderlist['username'];
                    $orderdetails['workname'] = $orderlist['workname'];
                    $orderdetails['confirm'] = $orderlist['confirm'];
                     $orderdetails['confirm_num'] = $orderlist['confirm'];
                    $orderdetails['order_sn'] = $orderlist['order_sn'];
                    $orderdetails['craft'] = $orderlist['craft'];

                    $orderdetails['place'] = $orderlist['place'];

                    $uptime = config('uptime'); 
                    $updatatime = $uptime[$orderlist['uptime']];
                    $orderdetails['update'] = $orderlist['update'] .' '.$updatatime;

                    $orderdetails['sanding'] = $orderlist['sanding'];
                    // $orderdetails['file_name'] = $orderlist['file_name'];
                    $orderdetails['num'] = $orderlist['num'];
                    $orderdetails['weight'] = $orderlist['weight'];
                    $orderdetails['material'] = $orderlist['material'];
                    $orderdetails['machine_number'] = $orderlist['machine_number'];
                    $orderdetails['date'] = $orderlist['date'];
                    // $orderdetails['programmer'] = $orderlist['programmer'];
                    $orderdetails['be_careful'] = $orderlist['be_careful'];



                    if($v['user_id'] ==  $data['user_id']){

                         $orderdetails['message'][$k]['static'] = 1;    

                    }else{

                         $orderdetails['message'][$k]['static'] = 0;    

                    }

                    $orderdetails['message'][$k]['mess_id'] = $v['mess_id'];
                    $orderdetails['message'][$k]['mess_list'] = $v['mess_list'];
                    $orderdetails['message'][$k]['mess_addtime'] = $v['add_time'];
                    $orderdetails['message'][$k]['real_name'] = $v['real_name'];
                     $orderdetails['message'][$k]['head'] = $domain.$v['head'];
                    // $orderdetails['message'][$k]['head'] = $v['head'];
                    $mess_img = explode(',',$v['mess_img']);
                    foreach ($mess_img as $n => $va ){
                            if(!$va == null)
                             $mess_img[$n] = $va;
                            else
                             $mess_img = array();

                        }    

                    $orderdetails['message'][$k]['mess_img'] = $mess_img;
                    
                }    


            }

                


            $list = array('data'=>$orderdetails,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);


             return json($list); 
             

         }else{

            return  json(echoArr(500, '非法请求'));

         }

    }

    // 添加订单留言
    // mess_list  留言
    // mess_img  评论图片
    // user_id 用户ID
    // order_id  订单ID
    public function orderMessageAdd()
    {

             $code = config('code');

             $msg = config('msg');

         if($this->request->isPost()){


           $data['mess_list'] = ''; 

            $data = input('post.');

             if(empty($data['user_id'] )){

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'用户id为空');

                return $error;

                exit;
            } 
             if(empty($data['order_id'] )){

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'订单ID为空');

                return $error;

                exit;
            } 

            if(empty($data['mess_img'])){

                    $data['mess_img'] = array();    
            }
            
  

    

           if(count( $data['mess_img']) > 3){

                    $error = array('code'=>$code['RequestFail'],'msg'=>'图片不能超过3张');

                    return json($error);

                    exit;

             }

            $userlist = Db::name('user')->where('user_id',$data['user_id'])->find();

             $request = Request::instance();

            $domain = $request->domain();

            $data['head'] = $domain.$userlist['head'];

            $data['real_name'] = $userlist['real_name'];

            $data['add_time'] = date("Y-m-d h:i:s");

            // var_dump($data);exit;
            
             $result = db('message')->insert($data);  

             $list = array('msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  

             return json($list);   

            
         }else{

           return  json(echoArr(500, '非法请求'));

         }

    }

    // 删除留言
    // user_id
    // mess_id
    public function delmessage()

        {

           if(request()->isPost()){

             $code = config('code');

             $msg = config('msg');

             $param = input('post.');

                // var_dump($param['user_id']);
             if(empty($param['user_id'])) {



                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'userid为空');

                return json($error);

                exit;
            } 

               if(empty($param['mess_id'])) {

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'mess_id为空');

                return json($error);

                exit;


               } 

                $grop_id = Db::name('message')->where('mess_id', $param['mess_id'])->field('user_id')->find();


                if($param['user_id'] == $grop_id['user_id']){

                 $result = db('message')->where('mess_id',$param['mess_id'])->delete();


                $list = array('msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  
   
                 return json($list); 


                }else{
                    $error = array('code'=>$code['RequestFail'],'msg'=>'无法删除非本用户的留言');

                    return json($error);

                    exit;
                }

            }else{


                    return  json(echoArr(500, '非法请求'));
  
            } 

        }



    // 确定订单
    // order_id
    // role_id
    // confirm   
    public function orderConfirm() {

            $code = config('code');

             $msg = config('msg');

        if($this->request->isPost()) {

    

              $data = input('post.');

             if(empty($data['order_id'] )){

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'订单id为空');

                 return $error;

                exit;
            } 

             if(empty($data['role_id'])){

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'权限id为空');

                return $error;

                exit;
            } 

             if(empty($data['confirm'] )){

                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'生产部值为空');

                return $error;

                exit;
            } 



          if($data['role_id'] == '4' || $data['role_id'] == '1'){

              $orderstatic = Db::name('order')->where('id',$data['order_id'])->field('static')->find();
              
                
              if($orderstatic['static'] == 2){


                $error = array('code'=>$code['OperationFailed'],'msg'=>'订单重复提交');

                return json($error);


                exit;
              }



             $query = Db::name('group')->where('type_id',$data['confirm'])->select();

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

                        $userlist[$v['orderby'] - 1]['order_id'] =  $data['order_id'];
                        
                    // 判斷時候否為空，如果不為空，那麼將數據拼接起來



                        if(!$userlist[$k]['order_id'] == null){

                             // var_dump( $b[0]) ;  
                             $userlist[$v['orderby'] - 1]['order_id'] =  $b[0];

                            $userlist[$v['orderby'] - 1]['order_id'] .= ','.$data['order_id'];

                            // var_dump($a);
                        }

                //3. 增加到等於可執行的最大數量時  將其賦值為1

                    if($userlist[$k]['orderby'] > $count){

                             $userlist[$k]['orderby'] = 1;
                      

                    }

                        // 修改订单状态.

                         $param['static'] = 2; 

                         // 
                             

                }  

                    // var_dump($order_id);

                   
                     $user = new UserModel();

                    

                     $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '2']);

                 
                 $user->updateall($userlist); 

                     $list = array('msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  

                     return json($list);  


             }else{

                  $list = array('msg'=>$msg['OperationFailed'],'code'=>'权限不足');  

                     return json($list);   
             }      

        } else{

           
           return  json(echoArr(500, '非法请求'));

        }

    }

   
     // 驳回订单
    // order_id
    // role_id

    public function orderEnd()
    {   


        if($this->request->isPost()){

             $code = config('code');

             $msg = config('msg');

              $data = input('post.');

              if(empty($data['order_id'] )){


                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'订单id为空');

                 return json($error);

                exit;

              }

                if(empty($data['role_id'])){

                   
                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'权限id为空');

                 return json($error);
                 exit;

              }

              if($data['role_id'] != 4){

                 $error = array('code'=>$code['OperationFailed'],'msg'=>'权限不足');
                 return json($error);
                 exit;

              }

               $result = Db::name('order')->where('id', $data['order_id'])->update(['static' => '3']);

                $list = array('msg'=>'订单驳回成功','code'=>$code['RequestSuccess']);  

                 return json($list);  


          }
         else{

           return  json(echoArr(500, '非法请求'));

          }

    }

    //     public function upload(){
    //     $info = $file->validate(['size'=>2000000,'ext'=>'jpg,png,gif'])->move(env('root_path') . 'public' . DIRECTORY_SEPARATOR . 'uploads');
    //     if($info){
    //         // Windows 转换反斜杠
    //         $img = $info->getSaveName();
    //         if("WIN" == substr(PHP_OS,0,3)){
    //             $img = str_replace('\\', '/', $img);
    //         }

    //         return echoArr(200, 'RequestSuccess', ['img' => $img]);
    //     }else{
    //         // 上传失败获取错误信息
    //         return echoArr(500, 'RequestFail');
    //     }
    // }

    //上传图片 
    // file 图片信息

        public function uploadimg()
    {



      if($this->request->isPost()){



             $request = Request::instance();

            $domain = $request->domain();

           $code = config('code');

            $msg = config('msg');      
            
           $file = request()->file('file');       

        if ($file) {


            $info = $file->move('public/upload/image/');

            $rule = array();


            $type = $info->check(['size'=>2000000,'ext'=>'jpg,png,gif']);

            if(!$type){

                $list = array('msg'=>$msg['OperationFailed'],'code'=>'图片上传失败');  

                 return json($list);  

            }

            if ($info) {

                $file = array();

                $file['src'] = $domain.'/'.'public/upload/image/'.$info->getSaveName();
                
                $list = array('data'=>$file,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  

                 return json($list);   
            }else{

                  $list = array('msg'=>$msg['OperationFailed'],'code'=>'上传失败');  

                 return json($list);   

            }
        }


    }else{

       
           return  json(echoArr(500, '非法请求'));

    }



    }


    // 搜索订单

    public function searchorder(){

         if($this->request->isPost()){
                       $code = config('code');

            $msg = config('msg');  

            $data = input('post.search');

             $orderlist = Db::name('order')->where('order_sn','like','%'.$data.'%')->whereor('username','like','%'.$data.'%')->select();  

            

              $list = array('data'=>$orderlist,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  
                return json($list);   

         }else{

             return  json(echoArr(500, '非法请求'));

         }

    }
    
 }