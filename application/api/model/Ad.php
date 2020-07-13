<?php

namespace app\index\model;

use think\Model;

class Ad extends Model
{
    //获取多条数据
   public  function getFind($id)
   {
      $data  = $this->whereIn('position_id',$id)->where('is_show',1)->field('ad_link ,ad_img')->select()->toArray();
      $temp =[];
      $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
      foreach ($data as $k=>$v)
      {
        if(!empty($v['ad_img']))
        { 
          $temp[$k]['imgUrl'] = $ip.'/uploads/'.$v['ad_img'];
        }
          $temp[$k]['url'] = $v['ad_link'];
      }
      return $temp;
   }

   /**
    * 获取新品推荐 banner 图
    */
   public function getNewBanner(){
       $img = $this -> where('position_id', 11) -> where('id', 17) -> value('ad_img as img');

       $domain = request() -> domain() . config('imgRoute');
       return $img ? $domain . $img : '';
   }
}
