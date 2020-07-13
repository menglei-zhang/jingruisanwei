<?php

namespace app\index\model;

use think\Model;

class GoodsSpecImg extends Model
{
    public  function getSpecAll($data, $domain)
    {
        // 商品规格
        $spec = $this -> spec($data['gId'], $domain);

        // 判断是否拥有绣字的规格
        $canEmbroider = [false, false];
        list($embroiderStyle, $embroider) = [[], []];
        foreach($spec as $k => $v){
            // 清除cate_id字段
            unset($spec[$k]['cate_id']);

            // 判断是否为绣字规格
            if(1 == $v['cate_id']){
                // 接口返回是否有此规格
                $canEmbroider[0] = true;

                // 清除描述字段
                $spec[$k]['list'] = array_reduce($spec[$k]['list'], function($result, $item){
                    unset($item['detail']);

                    $result[] = $item;

                    return $result;
                });
                $embroiderStyle[] = $spec[$k];

                unset($spec[$k]);
            }else if(2 == $v['cate_id']){
                // 接口返回是否有此规格
                $canEmbroider[1] = true;

                // 判断是否为绣字位置的规格
                $embroider[] = $spec[$k];

                unset($spec[$k]);
            }

        }
        
        // 接口返回是否有此规格
        $canEmbroider = $canEmbroider[0] && $canEmbroider[1] ? 1 : 0;

        // 返回的数据
        $data = [
            'style' => array_values($spec),
            'embroiderStyle' => $embroiderStyle,
            'embroider' => $embroider,
            'canEmbroider' => $canEmbroider
        ];

        return echoArr(200, '请求成功', $data);
     }

    /**
     * 商品规格
     * @param $gId      商品Id
     * @param $domain   当前域名
     * @return array    返回的当前商品的所有规格
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function spec($gId, $domain){
        // 商品选中的规格
        $goodsSpecImg = new GoodsSpecImg();
        $data = $goodsSpecImg -> where('goods_id', $gId) -> field('spec_item_id,img') -> select() -> toArray();

        // 单独将规格值 id 转换成一维数组
        $specImgId = array_column($data, 'spec_item_id');

        // 商品的规格值
        $specItem = new GoodsSpecItem();
        $specValue = $specItem -> whereIn('id', $specImgId) -> select();
        $specId = [];
        foreach($specValue as $k => $v){
            $specId[] = $v['spec_id'];
        }

        // 商品规格名称
        $spec = new GoodsSpec();
        $specName = $spec -> whereIn('id', $specId) -> field('id,spec_name,cate_id') -> select();

        // 商品规格
        $specification = [];
        foreach($specName as $k => $v){
            // 规格名称
            $temp = [
                'name' => $v['spec_name'],
                'cate_id' => $v['cate_id']
            ];
            foreach($specValue as $key => $val){
                // 匹配规格值对应的规格名
                if($val['spec_id'] == $v['id']){
                    $list = ['name' => $val['item'], 'id' => $val['id'], 'detail' => $val['describe']];

                    // 获取规格图片
                    foreach($data as $k1 => $v1){
                        if($v1['spec_item_id'] == $val['id']){
                            $list['img'] = '';

                            if($v1['img']){
                                $list['img'] = $domain . config('imgRoute') . $v1['img'];
                            }
                        }
                    }

                    $temp['list'][] = $list;
                }
            }

            $specification[] = $temp;
        }

        return $specification;
    }
}
