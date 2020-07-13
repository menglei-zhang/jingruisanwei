<?php

namespace app\index\model;

use think\Model;
use app\index\validate\UserAddress as Vali;
class UserAddress extends Model
{
    //获取多条数据
    public function getAddress($id)
    {
        $data = $this ->field('consignee as recipient, mobile as phone, address, is_default, id, province, city, district')->whereIn('user_id', $id)->select()->toArray();
        foreach($data as $key => $val){
          	$temp = [
            	$val['province'],
              	$val['city'],
              	$val['district']
            ];
          
          	unset($data[$key]['province']);
            unset($data[$key]['city']);
            unset($data[$key]['district']);
          	$data[$key]['region'] = $temp;
        }
      
        return echoArr(200, '请求成功', ['list' => $data]);
    }
    //删除一条数据
    public function delGet($id)
    {
       $status = $this->where('id',$id)->delete();
       if($status){
           $info['code'] =200;
           $info['msg'] = '删除成功';
       }else{
           $info['code'] =500;
           $info['msg'] = '删除失败';
       }
       return $info;
    }
  
    //添加,更改数据
    public function addGet($data)
    {
        $validate = new Vali();
        if(!$validate -> check($data)){
            return echoArr(500, $validate->getError());
        }
      
      	// 判断此次是否修改或新增
        $action = false;
        if(!empty($data['id'])){
            $action = true;
        }
      
      	// 判断当前数据是否设置为默认地址，是则修改之前默认地址
        if($data['is_default'] == 1){
            $default = ['is_default' => 0];
            $result = $this ->where('is_default', 1)->update($default);
        }
        $result = $this -> allowField(true) -> isUpdate($action) -> save($data);
      
        if(false === $result){
            return echoArr(500, '操作失败');
        }else{
            return echoArr(200, '请求成功');
        }
    }

}