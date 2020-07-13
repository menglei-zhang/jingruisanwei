<?php

namespace app\index\model;

use think\Model;
use app\index\model\Ad;
class AdPosition extends Model
{
    public  function  getFind()
    {
         $data =$this->where('position_name','banner')->field('id')->find()->toArray();
         $AdModel = new Ad();
         //获取banner图数据
         $bannerdata = $AdModel->getFind($data['id']);
         return $bannerdata;
    }
}
