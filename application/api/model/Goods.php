<?php
/**
 * Created by PhpStorm.
 * User: wzy12
 * Date: 2018/10/16
 * Time: 17:13
 */

namespace app\index\model;

use phpDocumentor\Reflection\DocBlock\Tags\Var_;
use think\Model;
use app\index\model\Cart;
use app\index\model\GoodsImg;
use app\index\model\Users;
use app\index\model\UserLevel;
use app\index\model\Intelligent;
use app\index\model\GoodsSpec;
use app\index\model\GoodsSpecPrice;
use app\index\model\GoodsSpecCate;
use app\index\model\GoodsSpecItem;
use app\index\model\Activity;
use app\index\model\ActivityGoods;
use app\index\validate\Cart as Vali;

class Goods extends Model
{
    protected $id = 'id';
    protected $on_time = 'on_time';
    protected $cate_id = 'cate_id';
     

    public function cate()
    {
        return $this->belongsTo('GoodsCate', 'cate_id', 'id', ['GoodsCate' => 'c', 'Goods' => 'g'], 'left');
    }

    public function resList($s)
    {
        $nowtimes = date('Y-m-d H:i:s', $s);
        $ustime = date("Y-m-d 00:00:00");
        $catime = strtotime($ustime);

        $query = $this->where('on_time', '>=', '$catime')->select();

        foreach ($query as $k => $v) {
            $query[$k]['cate_name'] = $v->cate->cate_name;
        }
        return $query;
    }

    public function resInset($data)
    {
        $query = $this->insert($data);
        return $query;
    }

    public function getGoodsAll($data)
    {
        $data = $this->where('mends_id', 0)
            ->where('is_recommend', $data)
          	->where('is_on_sale',1)
            -> order('id desc')
            -> limit(6)
            ->field('id,goods_name,shop_price,original_img')
            ->select()
            ->toArray();
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        $temp = [];
        foreach ($data as $k => $v) {
            if(!empty($v['original_img']))
            { 
              $temp[$k]['gImg'] = $ip.'/uploads/'.$v['original_img'];
            }
            $temp[$k]['gName'] = $v['goods_name'];
            $temp[$k]['gPrice'] = $v['shop_price'];
            $temp[$k]['gId'] = $v['id'];
        }

        return $temp;
    }

    public function getGoodsAllJ($data)
    {
        $data = $this->where('mends_id', 0)
            ->where('is_recommend', $data)
          	->where('is_on_sale',1)
            ->field('id as gId,original_img as gImg,goods_name as gName,shop_price as gPrice')
            -> order('id desc')
            ->select()
            ->toArray();

        $temp = [];
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        foreach ($data as $k => $v) {
           if(!empty($v['gImg']))
            {
                $data[$k]['gImg'] = $ip.'/uploads/'.$v['gImg'];
            }
        }
        return $data;
    }

    //根据条件，查询条件下的商品
    public function goodsAll($id)
    {
        $getGoodsAll = $this->whereIn('mends_id', $id)	->where('is_on_sale',1)->field('id,goods_name,shop_price,original_img')->select()->toArray();
   
    
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        $temp = [];
        foreach ($getGoodsAll as $k => $v) {
           if(!empty($v['original_img']))
            { 
              $temp[$k]['gImg'] = $ip.'/uploads/'.$v['original_img'];
            }
            $temp[$k]['gName'] = $v['goods_name'];
            $temp[$k]['gPrice'] = $v['shop_price'];
            $temp[$k]['gId'] = $v['id'];
        }
        return $temp;
    }

    //查询商品的新品“商品分类”
    public function goodsRecommend()
    {
        $data = $this->where('is_recommend', 1)	->where('is_on_sale',1)->field('id,goods_name,shop_price,original_img')->select()->toArray();
        $temp = [];
      	$ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'] ;
        foreach ($data as $k => $v) {
          
            if(!empty($v['original_img']))
            { 
              $temp[$k]['gImg'] = $ip.'/uploads/'.$v['original_img'];
            }
            $temp[$k]['gName'] = $v['goods_name'];
            $temp[$k]['gPrice'] = $v['shop_price'];
            $temp[$k]['gId'] = $v['id'];
        }
        return $temp;
    }

    public function goodsCateAll($cate_id, $status)
    {
        if (!empty($status)) {
            $data = $this->whereIn('cate_id', $cate_id)
                ->where('mends_id', 0)
                ->where('is_on_sale', 1)
                ->whereIn('is_recommend', $status)
                ->field('id,goods_name,shop_price,original_img')
                ->limit(5)
                ->select()
                ->toArray();
          
        } else {
            $data = $this->whereIn('cate_id', $cate_id)
                ->where('mends_id', 0)
                ->where('is_on_sale', 1)
                ->field('id,goods_name,shop_price,original_img')
                ->limit(5)
                ->select()
                ->toArray();
        }

        $temp = [];
        $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'];
        foreach ($data as $k => $v) {
            if(!empty($v['original_img']))
            {
                $temp[$k]['gImg'] = $ip.'/uploads/'.$v['original_img'];
            }
            $temp[$k]['gName'] = $v['goods_name'];
            $temp[$k]['gPrice'] = $v['shop_price'];
            $temp[$k]['gId'] = $v['id'];
        }
        return $temp;
    }


    public function goodsContent($id, $domain)
    {
        $data = $this->where('id', $id)->where('is_on_sale',1)->field('original_img,goods_name,shop_price,details')->find()->toArray();
      
         $ip =  $_SERVER['REQUEST_SCHEME'] . '://' .$_SERVER['SERVER_NAME'];
         if(!empty($data['original_img']))
            {
               $data['original_img'] = $ip.'/uploads/'.$data['original_img'];
            }

        // 商品详情
        $data['details'] = preg_replace_callback('/(<img.+?src=")(.*?)/', function ($matches) use ($domain){
            return $matches[1] . $domain;
        }, $data['details']);

        return $data ;
    }
  
  	// 加入购物车
  	public function addCart($data, $domain = null)
    {
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('add') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 刺绣内容
        if(!isset($data['embroiderValue'])){
            return echoArr(500, '参数错误');
        }else if(mb_strlen($data['embroiderValue'], 'utf-8') >= 10){
            return echoArr(500, '刺绣内容不能大于10个字');
        }

        // 判断购物车是否有此用户的商品呢
        $cart = new Cart();
        $where = [
            'user_id' => $data['user_id'],
            'goods_id' => $data['gId'],
            'spec_key' => $data['key'],
            'intelligent_id' => $data['int_id']
        ];

        // 判断是否为常规尺寸
        if(0 == $data['int_id']){
            $where['height'] = $data['height'];
            $where['weight'] = $data['weight'];
        }

        $temp = $cart -> where($where) -> field('id,goods_num,intelligent_id,goods_name,goods_id,goods_price,goods_num') -> find();
        if(!$temp || isset($data['shop'])){
            $action = false;

            // 判断是否为常规尺寸
            if(0 != $data['int_id']){
                // 智能量体处理
                $intelligent = new Intelligent();
                $where = [
                    'uid' => $data['user_id'],
                    'id' => $data['int_id']
                ];
                $result = $intelligent -> where($where) -> whereIn('status', '1,2') -> value('id');
                if(!$result){
                    return echoArr(500, '智能量体不符合');
                }
            }

            // 商品信息
            $temp = $this -> field('original_img,goods_sn,goods_name,market_price,shop_price as goods_price') -> find($data['gId']) -> toArray();

            // 会员折扣价
            $user = new Users();
            $levelId = $user -> where('id', $data['user_id']) -> value('level');
            $level = new UserLevel();
            $discount = $level -> where('id', $levelId) -> value('discount');
            $temp['member_goods_price'] = $temp['goods_price'] * $discount/100;

            // 商品规格中文名称
            $specName = $this -> specName($data['gId'], $data['key']);

            // 购物车完善信息
            $temp['goods_price'] = (float) $temp['goods_price'];
            $temp['spec_key'] = $data['key'];
            $temp['spec_key_name'] = $specName;
            $temp['user_id'] = (int) $data['user_id'];
            $temp['goods_id'] = (int) $data['gId'];
            $temp['goods_num'] = (int) $data['gNum'];
            $temp['add_time'] = time();
            $temp['market_price'] = (float) $temp['market_price'];
            $temp['goods_price'] = (float) $temp['goods_price'];
            $temp['intelligent_id'] = $data['int_id'];
            $temp['embroiderValue'] = $data['embroiderValue'];
            $temp['height'] = $data['height'];
            $temp['weight'] = $data['weight'];

            // 抢购商品
            if(isset($data['shop'])){
                $temp['isNormal'] = 1;

                if($data['activityId'] != 0){
                    $temp['activity_name'] = $this -> activityGoods($data['activityId']);
                }
            }
        }else{
            // 如果已有商品，则增加商品数量
            $action = true;

            $temp['goods_num'] += $data['gNum'];
            $temp = $temp -> toArray();
        }

        $res = $cart -> allowField(true) -> isUpdate($action) -> save($temp);
        if(false === $res){
            return echoArr(500, '请求失败');
        }else{
            $shop = [];

            // 立刻购买
            if(!$action){
                $cId = $cart -> id;
            }else{
                $cId = $temp['id'];
            }

            $shop = [
                'cId' => $cId,
                'gId' => $temp['goods_id'],
                'gNum' => $data['gNum'],
                'gName' => $temp['goods_name'],
                'gPrice' => $temp['goods_price'],
            ];

            // 商品主图
            $img = $this -> where('id', $temp['goods_id']) -> value('original_img');
            $shop['gImg'] = $domain . config('imgRoute') . $img;

            // 尺寸名称
            $intelligent = new Intelligent();
            $shop['gSize'] = $intelligent -> where('id', $temp['intelligent_id']) -> value('img_name');

            // 抢购商品
            if(isset($data['shop'])){
                $shop = [];
                $shop['cId'] = $cart -> id;

                // 商品详细信息
                $shop['gId'] = $data['gId'];
                $shop['gNum'] = $data['gNum'];
                $shop['gName'] = $temp['goods_name'];
                $shop['gPrice'] = $temp['goods_price'];
                $shop['gImg'] = $domain . config('imgRoute') . $temp['original_img'];

                // 尺寸名称
                $intelligent = new Intelligent();
                $shop['gSize'] = $intelligent -> where('id', $data['int_id']) -> value('img_name');
            }

            return echoArr(200, '请求成功', $shop);
        }
    }

    /**
     * 商品规格中文名
     */
    private function specName($gId, $key){
        // 用户选中的规格
        $itemIds = explode('_', $key);

        // 规格值
        $specItem = new GoodsSpecItem;
        $itemData = $specItem -> whereIn('id', $itemIds) -> field('id,spec_id,item') -> select() -> toArray();

        // 规格id
        $specIds = array_column($itemData, 'spec_id');

        // 规格名
        $spec = new GoodsSpec;
        $data = $spec -> whereIn('id', $specIds) -> select() -> toArray();

        // 规格中文名
        $result = array_reduce($data, function ($result, $item) use($itemData){
            $temp = $item['spec_name'] . ":";

            foreach($itemData as $k => $v){
                if($v['spec_id'] == $item['id']){
                    $temp .= $v['item'];
                }
            }

            $result .= ' ' . $temp;

            return $result;
        });

        return $result;
    }

    /**
     * 活动商品详情
     */
    public function activityGoods($aId){
        // 活动商品详情
        $activityGoods = new ActivityGoods();
        $aGoods = $activityGoods -> find($aId);

        // 活动名称
        $activity = new Activity();
        $config = $activity -> where('id', $aGoods['activity_id']) -> field('name,config_value') -> find();
        $configValue = unserialize($config['config_value']);

        // 活动描述
        $desc = $config['name'] . ' ';
        foreach($configValue['time'] as $k => $v){
            if($v['id'] == $aGoods['time_id']){
                $desc .= date('Y-m-d H:i:s', $v['start']) . ' ~ ' . date('Y-m-d H:i:s', $v['end']);
            }
        }

        return $desc;
    }

    /**
     * 抢购商品详情
     */
    public function snatchDetail($data, $domain){
        // 验证
        $validate = new Vali();
        if(!$validate -> scene('snatch') -> check($data)){
            return echoArr(500, $validate->getError());
        }

        // 活动的商品详情
        $activityGoods = new ActivityGoods();
        $aGoods = $activityGoods -> find($data['gId']);

        // 商品详情
        $goods = $this -> field('original_img,market_price as gOrigin,goods_name as gName,shop_price as gPrice,details') -> find($aGoods['goods_id']);

        // 商品主图
        $GoodsImgModel = new GoodsImg();
        $img = $GoodsImgModel->getImg($aGoods['goods_id']);
        $gImg = array_column($img,'img');
        array_unshift($gImg, $domain . config('imgRoute') . $goods['original_img']);

        // 当前活动结束时间
        $activity = new Activity();
        $configValue = $activity -> where('id', $aGoods['activity_id']) -> value('config_value');
        $configValue = unserialize($configValue);
        list($start, $end) = ['', ''];
        foreach($configValue['time'] as $k => $v){
            if($v['id'] == $aGoods['time_id']){
                $start = $v['start'];
                $end = $v['end'];
            }
        }

        // 商品详情
        $detail = preg_replace_callback('/(<img.+?src=")(.*?)/', function ($matches) use ($domain){
            return $matches[1] . $domain;
        }, $goods['details']);

        // 返回数据
        $temp = [
            'activityId' => $aGoods['id'],
            'gId' => $aGoods['goods_id'],
            'gImgs' => $gImg,
            'gSnatch' => $aGoods['goods_new_total'],
            'gOrigin' => $goods['gOrigin'],
            'gName' => $goods['gName'],
            'gPrice' => $goods['gPrice'],
            'gDetail' => $detail,
            'startTime' => $start,
            'endTime' => $end
        ];

        return echoArr(200, '请求成功', $temp);
    }
}
   