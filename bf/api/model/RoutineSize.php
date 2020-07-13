<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/3/31
 * Time: 16:35
 */

namespace app\index\model;


use think\Model;

class RoutineSize extends Model
{
    public function resList(){
        $list = $this -> field('height,weight,sex') -> order('height asc,weight asc') -> select() -> toArray();

        $result = ['male' => [], 'female' => []];
        foreach($list as $k=>$v){
            $str = 'male';
            if(1 == $v['sex']){
                $str = 'female';
            }
            unset($v['sex']);
            $result[$str][]=$v;
        }

        return echoArr(200, '请求成功', $result);
    }
}