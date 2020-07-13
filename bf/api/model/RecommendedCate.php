<?php

namespace app\index\model;

use think\Model;
use think\validate\ValidateRule;
use app\index\model\Goods;

class RecommendedCate extends Model
{
    /**
     * @return array|bool 输出格式数组
     * @$temp  最后输出的值
     * @$data  数据库查出的值
     */
    public function getFindAll()
    {
        $id = $this->where('cate_name','风格推荐')->field('id')->find()->toArray();
        if(empty($id['id']))
        {
            return false;
        }else{
            $data=$this->where('pid',$id['id'])
                        ->where('status',1)
                        ->field('id,cate_name,icon,title')
                        ->select()
                        ->toArray();
        }
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
      
        $temp= [];
        foreach ($data as $k =>$v)
        {
           if(!empty($v['icon']))
            { 
              $temp[$k]['sImg'] = $ip.'/uploads/'.$v['icon'];
            }
            $temp[$k]['sType'] =$v['cate_name'];
            $temp[$k]['sTitle']= $v['title'];
            $temp[$k]['sId'] =$v['id'];
        }
    return $temp ;
    }
    public function getFindAllC()
    {
        $id = $this->where('cate_name','场景推荐')->field('id')->find()->toArray();
        if(empty($id['id']))
        {
            return false;
        }else{
            $data=$this->where('pid',$id['id'])
                ->where('status',1)
                ->field('id,cate_name,icon,english_name')
                ->select()
                ->toArray();
        }
       $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        $temp= [];
        foreach ($data as $k =>$v)
        {
          if(!empty($v['icon']))
            { 
              $temp[$k]['sceImg'] = $ip.'/uploads/'.$v['icon'];
            }
          
            $temp[$k]['sceName'] =$v['cate_name'];
            $temp[$k]['sceEngName'] =$v['english_name'];
            $temp[$k]['sceId'] =$v['id'];
        }
        return $temp ;

    }

    public  function getGoodsAll($cate_name)
    {
        if(!empty($cate_name))
        {
             $data= $this->where('cate_name',$cate_name)->field('id')->find()->toArray();
             if(!empty($data))
             {
                 $goods_id = $this->where('pid',$data['id'])->field('id')->select()->toArray();
                 $id = array_column($goods_id,'id');
                 if($id)
                 {
                     return $id;
                 }else{
                     $info['code']= 300;
                     $info['msg'] = '该条件下没有满足条件的商品';
                     return $info ;
                 }
             }else{
                 $info['code']= 300;
                 $info['msg'] = '该条件下没有满足条件的商品';
                 return $info ;
             }
        }else{
            $info['code']= 300;
            $info['msg'] = '参数错误，服务器无响应！';
            return $info ;
        }
    }

    public function cateGoodsAll($cate_id, $domain){
        // 设计师分类列表
        $list = $this -> field('id,pid') -> select() -> toArray();

        // 当前分类id的父级id
        $pId = 0;
        foreach($list as $k => $v){
            if($v['id'] == $cate_id){
                $pId = $v['pid'];
            }
        }

        // 当前分类id and 分类id下的子分类id
        $data = cateFind($list, $pId, $cate_id);

        // 查找分类下的商品
        $ids = array_column($data, 'id');
        $goods = new Goods();
        $data = $goods -> whereIn('mends_id', $ids) -> where('is_on_sale', 1) -> field('original_img as gImg,goods_name as gName,shop_price as gPrice,id as gId') -> select() -> toArray();

        // 判断此分类下是否有商品
        if(!$data){
            return echoArr(500, '该分类无商品');
        }

        // 商品图片地址添加域名
        $result = array_reduce($data, function ($result, $temp) use($domain){
            $temp['gImg'] = $domain . config('imgRoute') . $temp['gImg'];

            $result[] = $temp;

            return $result;
        });

        return echoArr(200, '请求成功', $result);
    }
}
