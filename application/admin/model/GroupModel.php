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
namespace app\admin\model;

use think\Model;

class GroupModel extends Model
{
    // 确定链接表名
    protected $name = 'group';

    /**
     * 获取节点数据
     */
    public function getGroupInfo($id)
    {   


        $result = $this->field('id,group_name,type_id')->select();
        // $result = $this->field('id,group_name,type_id')->where('id',$id)->select();
        $str = '';


        // return $result;

        // 获取当前小组
        $group = new UserModel();
        $groups = $group->getGroupById($id);    

        if(!empty($groups)){
            $groups = explode(',', $groups);
        }

        foreach($result as $key=>$vo){

            $str .= '{ "id": "' . $vo['id'] . '", "pId":"' . $vo['type_id'] . '", "name":"' . $vo['group_name'].'"';

            

            if(!empty($groups) && in_array($vo['id'], $groups)){
                $str .= ' ,"checked":1';
            }

            $str .= '},';

        }

         return '[' . rtrim($str, ',') . ']';
    }
    // 取得子节点
    public function getgrouppeople($id)
    {
        $group = $this-> where('type_id',$id)->find();

        $where['group_id'] = $group['id'];

        $user = new UserModel();

        $users = $user->getUser($where); 
        
        return  $users; 
    }   


    /**
     * 获取节点数据
     * @return mixed
     */
    public function getGroupList()
    {
        return $this->field('id,group_name name,type_id pid')->select();
    }

    /**
     * 插入节点信息
     * @param $param
     */
    public function insertGroup($param)
    {
        try{

            $this->save($param);
            return msg(1, '', '添加节点成功');
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑节点
     * @param $param
     */
    public function editGroup($param)
    {
        try{

            $this->save($param, ['id' => $param['id']]);
            return msg(1, '', '编辑节点成功');
        }catch(PDOException $e){

            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 删除节点
     * @param $id
     */
    public function delGroup($id)
    {
        try{

            $this->where('id', $id)->delete();
            return msg(1, '', '删除节点成功');

        }catch(PDOException $e){
            return msg(-1, '', $e->getMessage());
        }
    }



}
