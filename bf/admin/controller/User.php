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


use app\admin\model\RoleModel;
use app\admin\model\UserModel;
use app\admin\model\GroupModel;

class User extends Base
{
    //用户列表
    public function index()
    {
        if(request()->isAjax()){

            $param = input('param.');

            $limit = $param['pageSize'];
            $offset = ($param['pageNumber'] - 1) * $limit;

            $where = [];
            if (!empty($param['searchText'])) {
                $where['user_name'] = ['like', '%' . $param['searchText'] . '%'];
            }
            $user = new UserModel();
            $selectResult = $user->getUsersByWhere($where, $offset, $limit);


             $status = config('user_status');

            // // 拼装参数
            foreach($selectResult as $key=>$vo){

              
                $selectResult[$key]['status'] = $status[$vo['status']];

                if( 1 == $vo['user_id'] ){
                    $selectResult[$key]['operate'] = '';
                    continue;
                }


                $selectResult[$key]['operate'] = showOperate($this->makeButton($vo['user_id']));

                if($selectResult[$key]['last_login_time'] == 0){

                    $selectResult[$key]['last_login_time'] = '未登录';

                } else{

                      $selectResult[$key]['last_login_time'] = date('Y-m-d H:i:s', $vo['last_login_time']);
                }

            }



            $return['total'] = $user->getAllUsers($where);  //总数据
            $return['rows'] = $selectResult;
               
            return json($return);
        }

        return $this->fetch();
    }

    // 添加用户
    public function userAdd()
    {
        if(request()->isPost()){

            $param = input('post.');

            $param['password'] = md5($param['password'] . config('salt'));
            $param['head'] = '/public/static/admin/images/profile_small.jpg'; // 默认头像

            $user = new UserModel();
            $flag = $user->insertUser($param);

            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }

        $role = new RoleModel();
        $this->assign([
            'role' => $role->getRole(),
            'status' => config('user_status')
        ]);

        return $this->fetch();
    }

    // 编辑用户
    public function userEdit()
    {
        $user = new UserModel();

        if(request()->isPost()){

            $param = input('post.');

            if(empty($param['password'])){
                unset($param['password']);
            }else{
                $param['password'] = md5($param['password'] . config('salt'));
            }
            $flag = $user->editUser($param);

            return json(msg($flag['code'], $flag['data'], $flag['msg']));
        }

        $id = input('param.id');

        
        $role = new RoleModel();

        $this->assign([
            'user' => $user->getOneUser($id),
            'status' => config('user_status'),
            'role' => $role->getRole()
        ]);
        return $this->fetch();
    }

    // 删除用户
    public function userDel()
    {
        $id = input('param.id');

        $role = new UserModel();

        $group = new GroupModel();

        $userlist = $role->getOneUser($id);

        $usercount = $group->getgrouppeople($userlist['group_id']);
        
        if(count($usercount) >= 1 ){

            return json(msg(-1, '', '删除的用户下有子级，无法删除'));

        }

        $flag = $role->delUser($id);

        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    



    public function giveAccess()
    {
        $param = input('param.');


        $group = new GroupModel();
        // 获取现在小组
        if('get' == $param['type']){

            $nodeStr = $group->getGroupInfo($param['id']);

            // var_dump($nodeStr);exit;

            return json(msg(1, $nodeStr, 'success'));
        }

        // 分配新权限
        if('give' == $param['type']){

            $doparam = [
                'id' => $param['id'],
                'group_id' => $param['rule']
            ];

            $user = new UserModel();
            $flag = $user->editAccess($doparam);

            $this->removRoleCache();
            return json(msg($flag['code'], $flag['data'], $flag['msg']));
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
            '编辑' => [
                'auth' => 'user/useredit',
                'href' => url('user/userEdit', ['id' => $id]),
                'btnStyle' => 'primary',
                'icon' => 'fa fa-paste'
            ],

            '删除' => [
                'auth' => 'user/userdel',
                'href' => "javascript:userDel(" .$id .")",
                'btnStyle' => 'danger',
                'icon' => 'fa fa-trash-o'
            ],

            '分配小组' => [
                'auth' => 'user/giveaccess',
                'href' => "javascript:giveQx(" .$id .")",
                'btnStyle' => 'info',
                'icon' => 'fa fa-institution'
            ],

        ];
    }
}
