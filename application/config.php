<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// $Id$

return [
    // 是否开启路由
    'url_route_on' => true,
    'trace' => [
        'type' => 'html', // 支持 socket trace file
    ],
    //各模块公用配置
    'extra_config_list' => ['database', 'route', 'validate'],
    //临时关闭日志写入
    'log' => [
        'type' => 'test',
    ],
    'app_debug' => true,
  
  	// 禁止访问模块
	//    'deny_module_list' => ['index'],

    // +----------------------------------------------------------------------
    // | 缓存设置
    // +----------------------------------------------------------------------
    'cache' => [
        // 缓存配置为复合类型
        'type'  =>  'complex',
        'default'	=>	[
            'type'	=>	'file',
            // 全局缓存有效期（0为永久有效）
            'expire'=>  0,
            // 缓存前缀
            'prefix'=>  'think',
            // 缓存目录
            'path'  =>  CACHE_PATH,
        ],
        'redis'	=>	[
            'type'	=>	'redis',
            'host'	=>	'127.0.0.1',
            // 全局缓存有效期（0为永久有效）
            'expire'=>  0,
            // 缓存前缀
            'prefix'=>  'think',
        ],
    ],


        'session'  => [
        'prefix'         => 'think',
        'type'           => '',
        'auto_start'     => true,
    ],

    //加密串
    'salt' => 'wZPb~yxvA!ir38&Z',
    //备份数据地址
    'back_path' => APP_PATH .'../back/',

      //存储路径
    'imgRoute'      => '/uploads/'

];
