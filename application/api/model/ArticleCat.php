<?php

namespace app\index\model;

use think\Model;
use app\index\model\Article;

class ArticleCat extends Model
{
    /**
     * 服务流程
     * @return mixed
     */
    public  function getDataAll()
    {
        $article = new Article();

        $list = $article -> resList(1);

        // 属于该分类下的文章
        $temp = [];
        foreach($list as $k => $v){
            if(1 == $v['id']){
                $temp['aSaleTerms'] = $v['desc'];
            }else if(2 == $v['id']){
                $temp['uTerms'] = $v['desc'];
            }else if(3 == $v['id']){
                $temp['sAgreement'] = $v['desc'];
            }else if(4 == $v['id']){
                $temp['photoHelp'] = $v['desc'];
            }
        }

        return echoArr(200, '请求成功', $temp);
    }
}