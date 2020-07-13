<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use think\Model;
use app\index\validate\Intelligent as Vali;
use app\index\controller\Phone;
use app\index\model\IntelligentAmount;

class Intelligent extends Model
{
    public function resFint($uid)
    {
        $data = $this->alias('p')
            ->join('intelligent_amount i', 'p.id = i.int_id')
            ->where('p.uid',$uid)
            ->where('i.status',1)
            ->select();

        return $data;
    }
   public  function getUserAll($uId)
     {
         // 当前用户量体
         $data = $this -> where('uid', $uId) -> order('id desc') -> field('id,height,weight,status,desc,img_name,add_time') -> select() -> toArray();

         // 去重，获取当前用户最新一条
//         $data = array_unset_tt($data, 'img_name');

       $temp = [];
       foreach($data as $k => $v){
           if(!isset($temp[$v['img_name']])) $temp[$v['img_name']] = [];

           // 审核成功
           if($v['status'] == 1){
               if(!isset($temp[$v['img_name']][0])) $temp[$v['img_name']][0] = $v;
           } else if($v['status'] == 0){
               // 审核中
               if(!isset($temp[$v['img_name']][1])) $temp[$v['img_name']][1] = $v;
           } else if($v['status'] == 2){
                // 审核失败
               if(!isset($temp[$v['img_name']][1])) $temp[$v['img_name']][1] = $v;
           }
       }

       $data = [];
       foreach($temp as $k => $v){
           foreach($v as $key => $val){
                $data[] = $val;
           }
       }

         // 用户的量体数据
         $arr = $this -> returnIntelligent($data);

         return echoArr(200, '请求成功', ['list' => $arr]);
     }
		
     public  function getDatasAll($data)
     {
         // 当前量体用户的所有尺寸
         $data = $this -> where('img_name', $data['name'])
                -> where('uid', $data['user_id'])
                -> order('id desc')
                -> field('id,height,weight,status,desc,img_name,add_time')
                -> select()
                -> toArray();

         if(!$data){
             return echoArr(200, '请求成功');
         }

         // 去除最新的一条
         array_shift($data);

         // 用户的量体数据
         $arr = $this -> returnIntelligent($data);

         return echoArr(200, '请求成功', ['list' => $arr]);
     }

    /**
     * 量体返回数据
     */
     private function returnIntelligent($data){
         // 当前量体数据
         $ids = [];
         foreach($data as $k => $v){
             $ids[] = $v['id'];
         }

         // 量体
         $intelligentAmount = new IntelligentAmount();
         $intelligentData = $intelligentAmount -> whereIn('int_id', $ids) -> select();

         // 用户的量体数据
         $arr = [];
         if($data && $intelligentData){
             foreach($data as $k => $v){
                 // 审核数据
                 $temp = [
                     'name' => $v['img_name'],
                     'id' => $v['id'],
                     'height' => $v['height'],
                     'weight' => $v['weight'],
                     'status' => $v['status'],
                     'reason' => $v['desc'],
                     'date' => $v['add_time'] ? date('Y-m-d H:i:s', $v['add_time']) : '',
                 ];

                 // 量体数据
                 $intelligentTest = [];
                 foreach($intelligentData as $key => $val){
                     if($v['id'] == $val['int_id']){
                         $intelligentTest = [
                             'neck' => $val['neck_circumference'],
                             'bust' => $val['bust'],
                             'middle' => $val['middle_waisted'],
                             'hipline' => $val['hipline'],
                             'armCircumference' => $val['arm_circumference'],
                             'wrist' => $val['wrist_circumference'],
                             'armLength' => $val['arm_length'],
                             'shoulder' => $val['shoulder_width'],
                             'theWaist' => $val['the_waist'],
                             'legLength' => $val['leg_length'],
                             'thighCircumference' => $val['thigh_circumference'],
                             'kneeCircumference' => $val['knee_circumference'],
                             'calfGirth' => $val['calf_girth'],
                             'condyleCircumference' => $val['condyle_circumference'],
                             'tongCrotch' => $val['tong_crotch'],
                         ];
                     }
                 }

                 $arr[] = array_merge($temp, $intelligentTest);
             }
         }

         return $arr;
     }

    public function addGetUserImg($data,$uid)
     {
          $validate = new Vali();
          if(!$validate->scene('info')->check($data)){
             return echoArr(0, $validate->getError());
          }

          $temp =[];
          $temp['uid'] =$uid;
          $temp['img_name'] = $data['name'];
          $temp['height'] =$data['height'];
          $temp['weight'] =$data['weight'];
          $temp['sex'] = $data['gender'];
          $temp['relationship'] =$data['relationship'];
          $temp['z_img'] =$data['z_img'];
          $temp['c_img'] =$data['c_img'];
          $temp['mosaic'] =$data['mosaic'];

          $result = $this -> save($temp);

          if(false === $result){
              return echoArr(500, '操作失败');
          }else{
              $result = [
                  'name' => $this['img_name'],
                  'id' => $this['id'],
                  'height' => $this['height'],
                  'weight' => $this['weight'],
                  'status' => 2,
              ];

              return echoArr(200, '请求成功', $result);
          }
     }
}

