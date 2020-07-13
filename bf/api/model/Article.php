<?php

namespace app\index\model;

use think\Model;

class Article extends Model
{
    public  function resList($catId){
        $list = $this -> where('cat_id', $catId) -> where('is_show', 1) -> field('id,title,desc') -> select();

        return $list;
    }
}