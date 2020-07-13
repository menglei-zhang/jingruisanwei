<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/7/5
 * Time: 9:50
 */

namespace app\admin\controller;


use app\index\controller\Statistics;
use think\Session;

class DataStatistics extends Base
{

    public function index(){
        if($this -> request -> isAjax()){
            $userId = Session::get('user_id');

            $data = input("get.");

            $statistics = new Statistics();
            $statistics -> page = true;
            $statistics -> size = $data['size'];

            $where = [];
            if($data['username']) {
                $where['workname'] = ['like', '%'. $data['username'] . '%'];
            }

            if(trim($data['time'])){
                $temp = explode(' - ', $data['time']);

                $where['addtime'] = ['between time', [$temp[0], $temp[1]]];
            }

            $result = $statistics -> dataStatistics($userId, $where);

            $result['data']['list'][] = [
                'userId' => '总计',
                'count' => array_sum(array_column($result['data']['list'], 'count')),
                'gram' => array_sum(array_column($result['data']['list'], 'gram')),
                'price' => array_sum(array_column($result['data']['list'], 'price')),
                'username' => '*',
                'role_name' => '*',
                'role_id' => '*',
                'head' => '*',
                'group_id' => '*'
            ];

            return json(['total' => $result['data']['count'], 'rows' => $result['data']['list']]);
        }

        return $this -> fetch();
    }
}