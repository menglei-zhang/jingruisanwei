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

class CustomerModel extends Model
{
    // 确定链接表名
    protected $name = 'customer';

    /**
     * 查询文章
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getCustomerByWhere($where, $offset, $limit)
    {
        return $this->where($where)->limit($offset, $limit)->order('cust_id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的文章数量
     * @param $where
     */
    public function getAllCustomer($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 添加文章
     * @param $param
     */
    public function addCustomer($param)
    {
        try{
            $result = $this->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{

                return msg(1, url('customer/index'), '添加客户成功');
            }
        }catch (\Exception $e){
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 编辑文章信息
     * @param $param
     */
    public function editCustomer($param)
    {
        try{

            $result = $this->save($param, ['cust_id' => $param['cust_id']]);

            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{
                
                return msg(1, url('customer/index'), '编辑客户成功');
            }
        }catch(\Exception $e){
            return msg(-2, '', $e->getMessage());
        }
    }

    /**
     * 根据文章的id 获取文章的信息
     * @param $id
     */
    public function getOneCustomer($id)
    {
        return $this->where('cust_id', $id)->find();
    }


    public function getCustomer($field)
    {
        return $this->field($field)->select();
    }

    /**
     * 删除文章
     * @param $id
     */
    public function delCustomer($id)
    {
        try{

            $this->where('cust_id', $id)->delete();
            return msg(1, '', '删除客户成功');

        }catch(\Exception $e){
            return msg(-1, '', $e->getMessage());
        }
    }
}
