<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/5/25
 * Time: 19:02
 */

namespace app\admin\validate;


use think\Validate;

class Upload extends Validate
{
    protected $rule = [
        'index|文件片数的当前位置' => 'require|number',
        'fileName|文件名称' => 'require',
        'tempName|文件临时名称'  =>  'require',
        'totalBockNum|文件片数的总数' => 'require|number',
    ];

    protected $scene = [
        'upload' => ['index', 'fileName', 'tempName', 'totalBockNum'],
    ];
}