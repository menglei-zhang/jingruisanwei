<?php

namespace app\index\model;

use think\Model;

class GoodsSpecPrice extends Model
{
    public function getFind($id)
    {
        $data = $this->whereIn('goods_id',$id)->field('key,key_name')->select()->toArray();
        if(empty($data)){
            return  $data =['code'=>200,'msg'=>'没有规格'];
        }
        foreach($data as $key => $val){
            $spec = $val['key_name'];
            $data = explode(" ", $spec);
            foreach($data as $k => $v){
                $arr = explode(':', $v);
                $temp[] = [
                    'specName' => $arr[0],
                    'specDetail' => $arr[1]
                ];
            }
        }
        
        return $temp;
    }

}
