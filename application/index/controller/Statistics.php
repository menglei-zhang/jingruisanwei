<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/7/5
 * Time: 13:29
 */

namespace app\index\controller;

use app\admin\model\GroupModel;
use app\admin\model\RoleModel;
use app\admin\model\UserModel;
use app\admin\model\OrderModel;
use think\paginator\driver\Bootstrap;

use think\Controller;

class Statistics extends Controller
{
    public $page = false;
    public $size = 10;
    private $isData = true;

    // 权限：查看全部
    private $whiteRole = [1, 5, 12, 13, 16];

    public function dataStatistics($userId, $searchKey){
        // 用户的权限id
        $userModel = new UserModel();
        $info = $userModel -> where('user_id', $userId) -> field('role_id,group_id') -> find();

        // 查看全部的订单
        $orderModel = new orderModel();
        $where = [];
        if(!in_array($info['role_id'], $this -> whiteRole)){
            switch ($info['role_id']) {
                case 4:
                    $groupModel = new GroupModel();
                    $groupIds = $groupModel -> where('type_id', $info['group_id']) -> column('id');
                    $userIds = $userModel -> whereIn('group_id', $groupIds) -> column('user_id');
                    array_push($userIds, $userId);

                    $where = [
                        'user_id' => ['in', $userIds],
                    ];
                    break;
                case 3:
                    $where = [
                        'user_id' => ['eq', $userId]
                    ];
                    break;
                default:
                    $this -> isData = false;
            }
        }



        if(!$this -> isData) return echoArr(500, '当前身份不允许查询');

        $list = $orderModel -> where($where) -> where($searchKey) -> select();

        // 添加所有组长
        $this -> addTeam($list, $userModel);

        // 处理数据
        $temp = $this -> handle($list);
        $count = count($temp);
        if($this -> page){
            $result = $this -> pages($temp);
            $temp = $result -> toArray()['data'];
        }

        return echoArr(200, '请求成功', ['list' => array_values($temp), 'count' => $count]);
    }

    /**
     * 添加所有组长
     *
     * @param $list         订单列表
     * @param $userModel    用户模型
     */
    private function addTeam(&$list, $userModel){
        $teamList = $userModel -> where('role_id', 4) -> select();
        foreach($teamList as $k => $v){
            $list[] = [
                'id' => '',
                'place' => 0,
                'workname' => $v['user_name'],
                'weight' => 0,
                'user_id' => $v['user_id'],
            ];
        }
    }

    /**
     * 数据处理
     *
     * @param $list
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    private function handle($list){
        $userModel = new UserModel();

        $temp = [];
        foreach($list as $k => $v){
            if(!isset($temp[$v['user_id']])) $temp[$v['user_id']] = [
                'userId' => $v['user_id'],
                'count' => 0,
                'gram' => 0,
                'price' => 0,
                'username' => $v['workname']
            ];

            if(!isset($v['pc_src'])) continue;

            $temp[$v['user_id']]['count']++;
            $temp[$v['user_id']]['gram'] += $v['weight'];
            $temp[$v['user_id']]['price'] += $v['place'];
        }

        // 所有的用户的权限
        $userIds = array_keys($temp);
        $userList = $userModel -> whereIn('user_id', $userIds) -> field('user_id,role_id,group_id,head') -> select();
        $roleIds = array_unique(array_column(collection($userList) -> toArray(), 'role_id'));

        // 权限名称
        $roleModel = new RoleModel();
        $roles = $roleModel -> whereIn('id', $roleIds) -> field('id,role_name') -> select();

        // 处理数据
        $domain = $this -> request -> domain();
        foreach($temp as $k => $v){
            list($roleId, $groupId, $roleName, $head) = ['', '', '', ''];

            foreach($userList as $key => $val){
                if($k == $val['user_id']){
                    $roleId = $val['role_id'];
                    $head = $val['head'] ? $domain . $val['head'] : '';
                    $groupId = $val['group_id'];

                    unset($userList[$k]);
                    break;
                }
            }

            foreach($roles as $val){
                if($roleId == $val['id']){
                    $roleName = $val['role_name'];
                }
            }

            $temp[$k]['role_name'] = $roleName;
            $temp[$k]['role_id'] = $roleId;
            $temp[$k]['head'] = $head;
            $temp[$k]['group_id'] = $groupId;
        }

        return $temp;
    }

    /**
     * 分页处理
     *
     * @param $list
     * @return \think\Paginator
     */
    private function pages($list){
        // 分页处理
        $data = $list ? $list : [];
        $curPage = input('page') ? input('page') : 1;//当前第x页，有效值为：1,2,3,4,5...
        $listRow = $this -> size;//每页默认10行记录

        $showData = array_chunk($data, $listRow, true);
        if($showData){
            $showData = $showData[$curPage - 1];
        }

        $url = $this -> request -> domain() . $this -> request -> baseUrl();
        $p = Bootstrap::make($showData, $listRow, $curPage, count($data), false, [
            'var_page' => 'page',
            'path'     => $url,//这里根据需要修改url
            'query'    => [],
            'fragment' => '',
        ]);
        $p->appends($_GET);

        return $p;
    }
}