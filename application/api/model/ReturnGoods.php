<?php

namespace app\index\model;

use think\Model;

class ReturnGoods extends Model
{
    public function getDataAll($user_id)
    {
          $data  = $this->whereIn('user_id',$user_id)
                        ->field('order_sn as oNumber,refund_time as applyTIme,describe,status')
                        ->select()
                        ->toArray();
           if(empty($data))
           {
               $info['code'] =500;
               $info['msg'] = '没有数据';
           }else{
               $info ['code'] =200 ;
               $info ['msg'] ='查询成功';
               $info ['list'] = $data;
           }

           return $info ;
    }
}
