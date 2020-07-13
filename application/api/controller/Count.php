<?php

namespace app\api\controller;

use app\admin\model\GroupModel;
use app\admin\model\UserModel;
use app\index\controller\Statistics;

class Count extends Base
{
	public function countList(){
	    if($this -> request -> isPost()){
            $data = input('post.');

            if(!isset($data['user_id']) || !$data['user_id']) return json(echoArr(1005, '请求失败'));
            if(!isset($data['updata']) || !$data['updata']) return json(echoArr(1005, '请求失败'));
            if(!isset($data['enddata']) || !$data['enddata']) return json(echoArr(1005, '请求失败'));

            // 时间查询
//            if($data['updata'] == $data['enddata']){
//                $where = [
//                    'addtime' => ['<=', $data['updata']]
//                ];
//            } else {
//                $where = [
//                    'addtime' => ['between time', [$data['updata'], $data['enddata']]]
//                ];
//            }

            $where = [
                'addtime' => ['between time', [$data['updata'], $data['enddata']]]
            ];


            $statistics =  new Statistics();
            $result = $statistics -> dataStatistics($data['user_id'], $where);
            if(200 != $result['code']) return json(echoArr(1002, $result['msg']));

            // 组长及以上人员的处理
            if(!$result['data']['list']) return json(echoArr(1001, '请求成功', ['list' => []]));
            if(3 != $data['role_id']){
                $leaderList = $this -> handle($result['data']['list']);
                $newData = $this -> integrationData($result['data']['list'], $leaderList);
            } else {
                $newData = [
                    [
                        'user' => [
                            $result['data']['list'][0]
                        ],
                    ]
                ];
            }

            return json(echoArr(1001, '请求成功', ['list' => $newData]));
        }
    }

    /**
     * 获取组长下的所有组员
     *
     * @param $data         未处理的数据
     * @return mixed
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function handle($data){
	    $userModel = new UserModel();
	    $groupModel = new GroupModel();

	    // 组长信息
        $leaderList = array_reduce($data, function($result, $item){
            if(4 == $item['role_id']) $result[] = [
                'userId' => $item['userId'],
                'groupId' => $item['group_id']
            ];

            return $result;
        }, []);
      	
        // 组员的分组
        $groupIds = array_column($leaderList, 'groupId');
        $temp = $groupModel -> whereIn('type_id', $groupIds) -> field('id,type_id')-> select();
        $groupMember = collection($temp) -> toArray();

        // 所有组员信息
        $groupIds = array_column($groupMember, 'id');
        $temp = $userModel -> whereIn('group_id', $groupIds) -> field('user_id,group_id') -> select();
        $groupList = collection($temp) -> toArray();

        // 处理数据
        foreach($leaderList as $k => $v){
            $groups = [];
            $users = [];

            // 分组信息
            foreach($groupMember as $key => $val){
                if($val['type_id'] == $v['groupId']){
                    $groups[] = $val['id'];
                }
            }

            // 组员
            foreach($groupList as $key => $val){
                if(in_array($val['group_id'], $groups)){
                    $users[] = $val;

                    unset($groupList[$key]);
                }
            }

            $leaderList[$k]['user'] = $users;
        }
      	
        return $leaderList;
    }

    /**
     * 整合数据
     *
     * @param $data             未处理的数据，所有员工
     * @param $leaderList       已处理的数据，组长包括组员
     * @return mixed
     */
    private function integrationData($data, $leaderList){
        $userIds = array_column($data, 'userId');
        foreach($leaderList as $k => $v){
            // 组长的具体信息
            foreach($data as $key => $val){
                if($v['userId'] == $val['userId']){
                    $leaderList[$k] = array_merge($v, $val);

                    unset($data[$key]);
                    break;
                }
            }

            // 组员的具体信息
            $user = [];
            foreach($v['user'] as $key => $val){
                if(!in_array($val['user_id'], $userIds)){
                    unset($leaderList[$k]['user'][$key]);

                    continue;
                }

                foreach($data as $k1 => $v1) {
                    if($val['user_id'] == $v1['userId']){
                        $user[] = array_merge($val, $v1);
                    }
                }
            }

            $leaderList[$k]['user'] = $user;
        }

        return $leaderList;
    }
}