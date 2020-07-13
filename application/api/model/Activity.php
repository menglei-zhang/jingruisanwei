<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/1/24
 * Time: 15:41
 */

namespace app\index\model;


use think\Model;

class Activity extends Model
{
    /**
     * 现在活动中的时间id
     * @return array
     */
    public function resList(){
        $config = $this -> where('id', 1) -> where('status', 1) -> value('config_value');
        if(!$config) return echoArr(500, '活动配置失败，请联系管理员');

        $config = unserialize($config);

        $ids = [];
        array_walk($config['time'], function ($v, $k) use(&$ids){
            if($v['start'] <= time() && $v['end'] > time()){
                $ids[] = $v['id'];
            }
        });

        return echoArr(200, '', $ids);
    }

    /**
     * 活动信息
     */
    public function activityInfo(){
        $info = $this -> where('id', 1)
                        -> where('status', 1)
                        -> field('name as title,english_name as englishTitle,activity_img as img')
                        -> find();

        if(!$info) {
            $info = [];
        } else {
            $domain = request() -> domain() . config('imgRoute');

            $info['img'] = $domain . $info['img'];
        }

        return $info;
    }
}