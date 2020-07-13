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

use app\admin\model\CustomerModel;

use app\admin\model\OrderModel;

use app\admin\model\UserModel;

use app\admin\model\GroupModel;

use think\Session;

use think\Db;


class Customer extends Base
{
    // 客户列表
    public function index()
    {
        if(request()->isAjax()){

            $param = input('param.');
            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (!empty($param['searchText'])) {
                $where['cust_name'] = ['like', '%' . $param['searchText'] . '%'];
            }
             $user = new UserModel();
             $userid = Session::get('user_id');  
            //  $this->assign('vo',$flag);
             $flag = $user->getOneUser($userid);
              // var_dump($flag['user_id']);

             if($flag['role_id'] == 3 ) {

               $where['user_id'] =  $flag['user_id'];

            }

            if($flag['role_id'] == 4 ) {

              $group = new GroupModel();
                $children = $group->getgrouppeople($flag['group_id']);
              
              	$a = [
                	'user_id' => [],
                ];
               foreach ($children as $key => $value) {
                  
                   $a['user_id'][$key] = $value['user_id'];
               }
              
              array_push($a['user_id'], $flag['user_id']);

                        
               $flag['user_id'] = implode(',', $a['user_id']);

               $where['user_id'] = array('in',$flag['user_id']);  

            }

            $customer = new CustomerModel();
            $customerlist = $customer->getCustomerByWhere($where, $offset, $limit);

            foreach($customerlist as $key=>$vo){
                 // $selectResult[$key]['thumbnail'] = '<img src="' . $vo['thumbnail'] . '" width="40px" height="40px">';

                $customerlist[$key]['operate'] = showOperate($this->makeButton($vo['cust_id']));
            }

            // 业务员名称
            $customerList = collection($customerlist) -> toArray();
            $userIds = array_column($customerList, 'user_id');
            $userList = $user -> whereIn('user_id', $userIds) -> field('user_id,user_name') -> select();

            $temp = [];
            foreach($userList as $key => $val){
                $temp[$val['user_id']] = $val['user_name'];
            }

            foreach($customerList as $k => $v){
                $customerList[$k]['user_name'] = $temp[$v['user_id']];
            }

            $return['total'] = $customer->getAllCustomer($where);  // 总数据
            $return['rows'] = $customerList;

            return json($return);
        }

        return $this->fetch();
    }

    // 添加客户
    public function customerAdd()
    {
        if(request()->isPost()){
            $param = input('post.');

             $customer = new CustomerModel();

             // 判断客户名是否重复
             
             $where['cust_name'] = $param['cust_name'];

             $count = $customer->getAllCustomer($where);

             if($count >= 1){

                 return json(msg(-1, '', '客户名重复'));

             }

           $userid = Session::get('user_id');  
          
            $user = new UserModel();

            $flag = $user->getOneUser($userid);   

            $param['user_id'] = $flag['user_id'];

            $param['addtime'] = date('Y-m-d H:i:s');

            // var_dump($param);exit;

            
            // exit;
             $flag = $customer->addCustomer($param);

            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }

        return $this->fetch();
    }

    public function customerEdit()
    {
        $customer = new CustomerModel();
        if(request()->isPost()){

            $param = input('post.');

            // unset($param['file']);

            // 判断客户名是否重复

             $where['cust_name'] = $param['cust_name'];

             $count = $customer->getAllCustomer($where);

             //if($count >= 1){

               //  return json(msg(-1, '', '客户名重复'));

             //}
            // var_dump($param);exit;

            $flag = $customer->editCustomer($param);

            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }

        $id = input('param.cust_id');

        $data = $customer->getOneCustomer($id);

        $this->assign('customer',$data);
        return $this->fetch();
    }



    public function customerDel()
    {
        $id = input('param.id');

        $customer = new CustomerModel();

        $cuslist = $customer->getOneCustomer($id);

        // var_dump($cuslist['cust_name']);

        $order = new OrderModel();

        $where['username'] = $cuslist['cust_name'];

        $count = $order->getAllorder($where);

        if($count >= 1){

            return json(msg(-1, '', '订单中还有此客户订单'));

        }

        // var_dump($count);
        // exit;   

        $flag = $customer->delcustomer($id);

        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // 上传缩略图
    public function uploadImg()
    {
        if(request()->isAjax()){

            $file = request()->file('file');
            // 移动到框架应用根目录/public/uploads/ 目录下
            $info = $file->move(ROOT_PATH . 'public' . DS . 'upload');
            if($info){
                $src =  '/upload' . '/' . date('Ymd') . '/' . $info->getFilename();
                return json(msg(0, ['src' => $src], ''));
            }else{
                // 上传失败获取错误信息
                return json(msg(-1, '', $file->getError()));
            }
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
                'auth' => 'customer/customerdel',
                'href' => "javascript:customerDel(" . $id . ")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o',
            ],

            '编辑' => [
                'auth' => 'customer/customeredit',
                'href' => url('customer/customerEdit', ['cust_id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],

        ];
    }
}
