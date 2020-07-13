<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
use app\index\model\Goods as a ;
class GoodsCate extends Model
{
       protected $pid = 'pid';
       protected $id = 'id';
    public function goods()
    {
        return $this->hasMany('Goods','id','cate_id');
    }

    public function classification()
    {

        $data = $this->where('pid','=','0')->select();

        $cate = $this->lar($data);
        $good = $this->lart($cate);

        return $data;
    }

    public function lar($data)
    {
        $id = [];
        $num =0;
        foreach ($data as $key=>$v){
            if($v['pid'] == 0){
                $id[] = $v['id'];
                $cate_name[] =$v['cate_name'];
                $name[]= $this->where('pid','=' ,$id[$num])->select();
                $num=$num+1;
            }
        }
        return  $name;
    }

    public function lart($data)
    {

        $id = [];
        $num =0;
        foreach ($data as $key=>$v){
            if(!empty($v[$num])) {
                $id[] = $v[$num]['id'];
                $cate_name[] = $v[$num]['cate_name'];
                $goods = new a();
                $name[] = $goods->where('cate_id', '=', $id[$num])->select();
                $num = $num + 1;
            }
        }
        return  $name;
    }

    public function  menuTree()
    {
        $menuDao = new MenuDao();
        $cateRes = $menuDao->where(['is_delete'=>self::IS_DELETE_ZERO])->order('id ASC')->select();
        return $this->menuSort(json_decode(json_encode($cateRes),true));
    }

    public function menuSort($cateRes,$pid=0,$level=0)
    {
        static $arr = [];
        foreach ($cateRes as $key => $value) {
            if($value['pid'] == $pid){
                $value['level'] = $level;
                $value['menu_name'] = str_repeat('&nbsp;&nbsp;',$level).'|--'.$value['menu_name'];
                $arr[] = $value;
                //进行递归
                $this->menuSort($cateRes,$value['id'],$level+2);
            }
        }
        return $arr;
    }

    public function getCateName()
    {
       $data = $this->field('id,cate_name,pid')->select()->toArray();
        return $data;
    }

   public function getImg($id)
    {
        $img = $this->where('id',$id['cate_id'])->where('status',1)->field('icon,body_img')->find()->toArray();
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        $temp = [
            'gImg' => '',
            'body_img' => '',
        ];
        if(!empty($img['icon']))
        {
            $temp['gImg'] = $ip.'/uploads/'.$img['icon'];
        }
        if(!empty($img['body_img']))
        {
            $temp['body_img'] = $ip.'/uploads/'.$img['body_img'];
        }
        return $temp;
    }
  
  
    public function getImgs($id)
    {
        $img = $this->whereIn('id',$id)->field('icon')->select()->toArray();
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        $temp =[];
        foreach ($img as $k=>$v)
        {
            if(!empty($v['icon']))
            {
                $temp[$k]['gImg'] = $ip.'/uploads/'.$v['icon'];
            }
        }
        return $temp;
    }
    public  function getHome()
    {
        $res = ['0'=>54,'1'=>58];
        $list = $this->whereIn('id',$res)->field('icon')->select()->toArray();
        $goods = $this->whereIn('pid',$res)->field('id')->select()->toArray();
        $cate_id = array_column($goods,'id');
        if(!empty($goods))
        {
            $GoodsModel = new a();
            $data = $GoodsModel ->whereIn('cate_id',$cate_id)->where('mends_id',0)->order('sort desc')->limit(4)->field('id,goods_name,shop_price,original_img')->select()->toArray();
            $temp = [];
            $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
            foreach ($data as $k => $v) {
               if(!empty($v['original_img']))
                {
                    $temp[$k]['gImg'] = $ip.'/uploads/'.$v['original_img'];
                }
                $temp[$k]['gName'] = $v['goods_name'];
                $temp[$k]['gPrice'] = $v['shop_price'];
                $temp[$k]['gId'] = $v['id'];
            }
            return $temp ;
        }

    }




}



