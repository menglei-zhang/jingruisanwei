<?php

namespace app\api\controller;

use think\Db;

use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use app\admin\model\CustomerModel;

use think\Request;

class Count extends Base
{


	// 统计列表
	// updata  开始年
	// enddata  结束年
	// user_id 用户id
	// role_id 权限id

	public function countList(){


		if($this->request->isPost()){

	         $code = config('code');

             $msg = config('msg');

			$data = input('post.');

			$count = array();

            if(empty($data['role_id'])){

                   
                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'权限id为空');

                 return json($error);


              }
          if(empty($data['user_id'])){

                   
                 $error = array('code'=>$code['ParamEmpty'],'msg'=>'用户id为空');

                 return json($error);


              }	

               $user = new UserModel();

              $request = Request::instance();

              $domain = $request->domain();
			// 组员

			if($data['role_id'] == 3){

				$order_id = Db::name('order')->where('user_id',$data['user_id'])->field('id')->select(); 

				foreach ($order_id as $key => $value) {
						
						$order_id[$key] = $value['id'];
						
					}	
							

				  $order_id_list = implode(',',$order_id);   


		  		$data['enddata'] = date('Y-m-d', strtotime('+1 day', strtotime($data['enddata'])));
				
		  		// foreach ($order_id as $key => $value) {
		  			$count['place'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('place');	
					$count['weight'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('weight');	
					$count['all'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('num');	
			
		  		// }

		  	 		 $userlist = $user->getOneUser($data['user_id']);	 

		  	 		 // 头像

		  	 		$count['head'] = $domain.$userlist['head'];

		  	 		$count['workname'] = $userlist['real_name'];

	  	}


	  		if($data['role_id'] == 4){

	  			  
	  				  $group_leader_list = Db::name('user')->where('user_id',$data['user_id'])->field('group_id')->find();



	  				  $group_leader_people = Db::name('group')->where('type_id',$group_leader_list['group_id'])->field('id')->select();


	  				  foreach ($group_leader_people as $key => $value) {
	  				  	
	  				  		$group_leader_people_list = Db::name('user')->where('group_id',$value['id'])->select();



	  				  }	
			  		foreach ($group_leader_people_list as $k => $v) {
	  				 	 			
	  				  			 // var_dump($v['user_id']);

	  				  			$group_leader_people_order[$k]['order_id'] = Db::name('order')->where('user_id',$v['user_id'])->field('id')->select();

							   	$group_leader_people_order[$k]['head'] = $v['head'];

							    $group_leader_people_order[$k]['workname'] = $v['real_name'];

		  			
  				  		}	
	  					
	  					
  				  		 
  				  		foreach ($group_leader_people_order as $key => $value) {

  				  				$order_id_any = array();
  				  			  // $order_id[$key] = $value['order_id'];

	  			  			  	foreach ($value['order_id'] as $k => $v) {
	  			  			  	 		
  			  			  			  $order_id_any[] = $v['id'];

	  			  			  	 } 	
								
	  			  			  	 // var_dump($order_id_any);

	  			  			   $order_id_list = implode(',',$order_id_any); 


						  		$data['enddata'] = date('Y-m-d', strtotime('+1 day', strtotime($data['enddata'])));
								
						  		// foreach ($order_id as $key => $value) {
					  			$count[$key]['place'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('place');	
								$count[$key]['weight'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('weight');	
								$count[$key]['all'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('num');	
								
								 // 头像

								 $count[$key]['head'] =$domain.$value['head'];
								// $count[$key]['head'] =$value['head'];

								$count[$key]['workname'] = $value['workname'];	

  								$count[$key]['detail'] = array();
  				  		}
  				  			

  				  		// var_dump($count);
	  		}

	  		if($data['role_id'] == 5 || $data['role_id'] == 16 ){


  				  $group_leader_list = Db::name('user')->where('user_id',$data['user_id'])->field('group_id')->find();



  				  $group_leader_people = Db::name('group')->where('type_id',$group_leader_list['group_id'])->field('id')->select();


  				  	

  	  				  foreach ($group_leader_people as  $value) {
	  				  	
	  				  		$group_leader_lists[]['user_id'] = Db::name('user')->where('group_id',$value['id'])->select();



	  				  }	


	  				  
	  				  foreach ($group_leader_lists as $key => $value) {
	  				  	
	  					

	  						 
							  foreach($value['user_id'] as $k => $v){

	    				  		$group_leader_info[$key]['head'] = $v['head'];

							    $group_leader_info[$key]['workname'] = $v['real_name'];



							    $group_leader_info[$key]['order_id'] = Db::name('order')->where('user_id',$v['user_id'])->field('id')->select();



							    $group_child  = Db::name('group')->where('type_id',$v['group_id'])->field('id')->select();

				     				  foreach ($group_child as $x => $z ) {
		  				  	
		  				  				 $group_leader_info[$key]['children'] = Db::name('user')->where('group_id',$z['id'])->select();
	

		  						  }	

							}

	  				  }
	  			   

	  			   
                   foreach ($group_leader_info as $key => $value) {
						
                   	// var_dump($key);

                   		 foreach ($value['children'] as $k => $v) {


               		 		 $group_leader_people_order[$key][$k]['order_id'] = Db::name('order')->where('user_id',$v['user_id'])->field('id')->select();



						   	 $group_leader_people_order[$key][$k]['head'] = $v['head'];

						     $group_leader_people_order[$key][$k]['real_name'] = $v['real_name'];


                   		 }
                   		  	  
					  
			  				    $order_id_any = array();
  				  			  // $order_id[$key] = $value['order_id'];

	  			  			  	foreach ($value['order_id'] as $k => $v) {
	  			  			  	 		
  			  			  			  $order_id_any[] = $v['id'];

	  			  			  	 } 	
								
	  			  			  	

	  			  			   $order_id_list = implode(',',$order_id_any); 


						  		$data['enddata'] = date('Y-m-d', strtotime('+1 day', strtotime($data['enddata'])));

						  		$group_leader_info[$key]['place'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('place');	
								$group_leader_info[$key]['weight'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('weight');	
								$group_leader_info[$key]['all'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('num');	
						

                   		    
                   }
  				  	 
                




		  		foreach ($group_leader_people_order as $key => $value) {

						// var_dump($value);  

									$count[$key]['place'] = $group_leader_info[$key]['place'];
									$count[$key]['weight'] = $group_leader_info[$key]['weight'];
									$count[$key]['all'] = $group_leader_info[$key]['all'];
						  			$count[$key]['head'] = $domain.$group_leader_info[$key]['head'];
						  			$count[$key]['workname'] = $group_leader_info[$key]['workname'];

	  		  			  	foreach ($value as $k => $v) {
	  			  			  	 		
		  			  			  $order_id_any = $v['order_id'];

		  			  			  $order_id_any_list = array();

			  			  			  foreach ($order_id_any as $x => $z) {
			  			  			  	
			  			  			  	    $order_id_any_list[$x] = $z['id'] ;

			  			  			  }
		  			  			     $order_id_list = implode(',',$order_id_any_list); 
			  		
						  			$data['enddata'] = date('Y-m-d', strtotime('+1 day', strtotime($data['enddata'])));  

		  						$count[$key]['detail'][$k]['place'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('place');	
								$count[$key]['detail'][$k]['weight'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('weight');	
								$count[$key]['detail'][$k]['all'] = Db::table('snake_order')->where('id','in',$order_id_list)->whereTime('update', 'between', [$data['updata'],$data['enddata']])->sum('num');	
								$count[$key]['detail'][$k]['head'] = $domain.$value[$k]['head'];
								$count[$key]['detail'][$k]['workname'] = $value[$k]['real_name'];


			  			  } 

		

							

					  			 
			  				
		  		}

			  		
	                   

	  		}


             $list = array('data'=>$count,'msg'=>$msg['RequestSuccess'],'code'=>$code['RequestSuccess']);  

              return json($list); 

            exit;

		}else{

			  return  json(echoArr(500, '非法请求'));

		}

	}

}



