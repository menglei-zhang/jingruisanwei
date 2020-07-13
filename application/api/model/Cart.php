<?php

namespace app\index\model;

use think\Model;
use app\index\model\Goods;
use app\index\model\Intelligent;
use app\index\validate\Cart as Vali;
class Cart extends Model
{
    /**
     * 商品列表
     */
   	public function getCart($userId, $domain)
    {
        $list = $this -> where('user_id', $userId)
            -> field('id as cId,goods_price as gPrice,goods_id as gId,goods_name as gName,goods_num as gNum,intelligent_id')
            -> select()
            -> toArray();

        // 商品的主图
        $ids = [];
        foreach($list as $k => $v){
            $ids[] = $v['gId'];
        }
        $goods = new Goods();
        $goodsList = $goods -> whereIn('id', $ids) -> field('id,original_img,give_integral') -> select();

        // 量体的尺寸
        $ids = [];
        foreach($list as $k => $v){
            $ids[] = $v['intelligent_id'];
        }
        $intelligent = new Intelligent();
        $intelligentList = $intelligent -> whereIn('id', $ids) -> field('id,img_name') -> select();

        foreach($list as $k => $v){
            unset($list[$k]['intelligent_id']);

            // 商品主图加上域名
            foreach($goodsList as $key => $val){
                if($v['gId'] == $val['id']){
                    $list[$k]['gImg'] = $domain . config('imgRoute') . $val['original_img'];
                }
            }

            // 量体尺寸
            foreach($intelligentList as $key => $val){
                if($v['intelligent_id'] == $val['id']){
                    $list[$k]['gSize'] = $val['img_name'];
                }
            }
        }

        return echoArr(200, '请求成功', $list);
    }

    /**
     * 购物车数量修改
     */
  	public function cartNum($data)
    {
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('editNum') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 更改购物车数量
        $temp['id'] = $data['cId'];
        $temp['goods_num'] = $data['gNum'];
        $temp['user_id'] = $data['user_id'];
        $res = $this -> isUpdate(true) -> save($temp);

        if(false === $res){
            return echoArr(500, '请求失败');
        }else{
            return echoArr(200, '请求成功');
        }
    }


    /**
     * 删除购物车商品
     */
    public function del($data){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('del') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        $ids = explode('_', $data['cId']);
        $result = $this -> destroy($ids);
        if(false === $result){
            return echoArr(500, '操作失败');
        }

        return echoArr(200, '请求成功');
    }
}
