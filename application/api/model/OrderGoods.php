<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
class OrderGoods extends Model
{
   public  function getGoodsDataAll($order_id)
    {
        $data = $this->alias('p')
             ->join('frame_goods g' ,'p.goods_id = g.id')
             ->field('goods_num as gNum,  p.goods_name as gName, original_img as gImg, g.id as gId')
             ->whereIn('order_id',$order_id)
             ->select()
             ->toArray();
        foreach ( $data as $k =>$v)
       {
           $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
           if(!empty($v['gImg']))
           {
               $data[$k]['gImg'] = $ip.'/uploads/'.$v['gImg'];
           }
       }

       return  $data ;
    }
}

