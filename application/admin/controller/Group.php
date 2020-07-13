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
use think\Session;

use think\Db;     
use app\admin\model\GroupModel;
use app\admin\model\RoleModel;
    
class Group extends Base
{ 
    // 节点列表
    public function index()
    {
        if(request()->isAjax()){

            $group = new GroupModel();
            $groups = $group->getGroupList();
                  
            $groups = getTree(objToArray($groups), false);
            return json(msg(1, $groups, 'ok'));
        }
        
        $role_name = Db::name('role')->select();

        // dump($role_name);exit;

       $this->assign('rolename',$role_name);

        return $this->fetch();
    }

    // 添加节点
    public function groupAdd()
    {

    if(request()->isAjax()){

        $param = input('post.');

        $group = new GroupModel();

  
 
        $where['group_name'] = $param['group_name'];

        $count = Db::name('group')->where($where)->count();



        if($count >= 1){

            return json(msg(-1, '', '名字重复'));

            exit;
        }

        $flag = $group->insertGroup($param);

        $this->removRoleCache();

        return json(msg($flag['code'], $flag['data'], $flag['msg']));

       }
        

    }

    // // 编辑节点
    public function groupEdit()
    {
        $param = input('post.');

        $group = new groupModel();
        $flag = $group->editgroup($param);
        $this->removRoleCache();
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }

    // // 删除节点
    public function groupDel()
    {
        $id = input('param.id');

        $role = new GroupModel();
        $flag = $role->delGroup($id);
        $this->removRoleCache();
        return json(msg($flag['code'], $flag['data'], $flag['msg']));
    }
}
