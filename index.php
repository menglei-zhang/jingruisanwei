<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Authorization, tokenAccess, Content-Type, If-Match, If-Modified-Since, If-None-Match, If-Unmodified-Since, X-Requested-With');
header('Access-Control-Allow-Methods: GET, POST, PATCH, PUT, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1728000');

if("OPTIONS" == $_SERVER['REQUEST_METHOD']){
  return;
}

// 定义应用目录
define('APP_PATH', __DIR__ . '/application/');
// 开启调试模式（这个无所谓）
define('APP_DEBUG', true);
// 加载框架引导文件
require __DIR__ . '/thinkphp/start.php';
