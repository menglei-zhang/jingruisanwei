<?php

namespace app\index\model;

use think\Model;

class GoodsImg extends Model
{
    public function getImg($goods_id)
    {
        $img = $this ->where('goods_id',$goods_id)->field('img')->select()->toArray();

         $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'];
      $temp =[];
        foreach($img as $k =>$v)
        {
           if(!empty($v['img']))
            {
                $temp[$k]['img'] = $ip.'/uploads/'.$v['img'];
            }
        }
        return $temp;
    }
}
