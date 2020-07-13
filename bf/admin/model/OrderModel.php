<?php
// +----------------------------------------------------------------------
// | snake
// +----------------------------------------------------------------------
// | Copyright (c) 2016~2022 http://baiyf.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: NickBai <1902822973@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\model;

use think\Model;

use think\Db;    

class orderModel extends Model
{
    // 确定链接表名
    protected $name = 'order';

    /**
     * 查询订单
     * @param $where
     * @param $offset
     * @param $limit
     */
    public function getorderByWhere($where, $offset, $limit)
    {   

            
        
        return $this->where($where)->limit($offset, $limit)->order('id desc')->select();
    }

    /**
     * 根据搜索条件获取所有的订单数量
     * @param $where
     */
    public function getAllorder($where)
    {
        return $this->where($where)->count();
    }

    /**
     * 添加订单
     * @param $param
     */
    public function addorder($param)
    {
        try{
            // $result = $this->validate('orderValidate')->save($param);


            $result = $this->save($param);
            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{

                return msg(1, url('order/index'), '添加订单成功');
            }
        }catch (\Exception $e){
            return msg(-2, '', $e->getMessage());
        }
    }


    // 添加订单留言

    // public function addMssage($param)
    // {
    //     try{

    //         $result = $this->save($param);

    //         if(false === $result){
    //             // 验证失败 输出错误信息
    //             return msg(-1, '', $this->getError());
    //         }else{

    //             return msg(1, url('order/index'), '添加文章成功');
    //         }
    //     }catch (\Exception $e){
    //         return msg(-2, '', $e->getMessage());
    //     }
    // }


    /**
     * 编辑订单信息
     * @param $param
     */
    public function editorder($param)
    {
        try{

            $result = $this->save($param, ['id' => $param['id']]);

            if(false === $result){
                // 验证失败 输出错误信息
                return msg(-1, '', $this->getError());
            }else{

                return msg(1, url('order/index'), '编辑订单成功');
            }
        }catch(\Exception $e){
            return msg(-2, '', $e->getMessage());
        }
    }


    //     public function tianjia($data)
    // {
    //     //halt($data);
    //     $count = count($data['pc_src']);//获取传过来有几张图片
    //     if($count){
    //         for($i = 0;$i< $count;$i++){
    //             $data['pics'][]=array("src"=>$data['pc_src'][$i]);
    //         }
    //         $data['pics'] = json_encode($data['pics']);
    //         //$data['cc'] = json_decode($data['bb']);
    //         //halt($data);
    //     }
         
    //     $result = $this->validate(true)->allowField(true)->save($data);
    //     if($result){
    //         // 验证失败 输出错误信息
    //         return ['valid'=>1,'msg'=>'添加成功'];
    //         //dump($this->getError());
    //     }else{
    //         return ['valid'=>0,'msg'=>$this->getError()];
    //     }
    // }

    /**
     * 根据订单的id 获取信息
     * @param $id
     */
    public function getOneorder($id)
    {
        return $this->where('id', $id)->find();
    }

    /**
     * 删除订单
     * @param $id
     */
    public function delOrder($id)
    {
        try{

            $this->where('id', $id)->delete();
            return msg(1, '', '删除订单成功');

        }catch(\Exception $e){
            return msg(-1, '', $e->getMessage());
        }
    }



    // 查询指定时间内订单数量

    public function dataorder($month,$year,$where)
    {
      	$max_day = date('t');	//当月最后一天
        //构造每天的数组
        $days_arr = array();
        for($i=1;$i<=$max_day;$i++){
          array_push($days_arr, $i);
        }
        
        //查询
         foreach ($days_arr as $k => $val){
            // var_dump($k);
            // var_dump($val);
          $min = $year.'-'.$month.'-'.$val.' 00:00:00';
          $max = $year.'-'.$month.'-'.$val.' 23:59:59';
            // $sql = "select count(*) as total_num,sum(`place`) as amount from `snake_order` where `addtime` >= '{$min}' and `addtime` <= '{$max}'";

          $a[$val] = Db::table('snake_order')->whereTime('addtime', 'between', [$min, $max])->where($where)->count();

         }  

         return $a;
    } 


}
