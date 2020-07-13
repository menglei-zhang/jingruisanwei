<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/1/24
 * Time: 15:41
 */

namespace app\index\model;


use think\Model;

class ActivityGoods extends Model
{
    /**
     * 商品列表
     */
    public function resList(){
        $activity = new Activity;

        // 获取时间活动id
        $result = $activity -> resList();
        if(200 != $result['code']) return $result;

        $list = $this -> alias('ag') -> where('ag.activity_id', 1)
                -> where('ag.is_on_sale', 1)
                -> whereIn('ag.time_id', $result['data'])
                -> join('Goods g', 'g.id = ag.goods_id')
                -> order('ag.id desc')
                -> field('ag.id as gId,g.goods_name as gName,ag.goods_new_total as gPrice,g.original_img as gImg')
                -> select() -> toArray();

        $domain = request() -> domain() . config('imgRoute');
        array_walk($list, function(&$v, $k, $domain){
            if($v['gImg']) $v['gImg'] = $domain . $v['gImg'];
        }, $domain);

        return echoArr(200, '请求成功', ['list' => $list]);
    }
}