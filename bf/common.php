<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用公共文件

// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

// curl请求
function sendCURL($url, $json = false, $ssl = false, $header = false){
    $cl = curl_init();
    curl_setopt($cl, CURLOPT_URL, $url);
    if($json){
        curl_setopt($cl, CURLOPT_POST, 1);
        curl_setopt($cl, CURLOPT_POSTFIELDS, $json);
    }
    if($header){
        curl_setopt($cl, CURLOPT_HTTPHEADER, $header);
    }
    curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, $ssl);
    curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, $ssl);
    curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
    $res = curl_exec($cl);
    curl_close($cl);
    return $res;
}

// 输出验证数据格式
function echoArr($code, $msg, $data = []){
    return [
        'code' => $code,
        'msg' => $msg,
        'data' => $data
    ];
}

// 用户密码加密
function encryption($pass){
    return md5(config('passConfig') . $pass);
}

// 获取所有控制器名
function getControllers($dir) {
    $pathList = glob($dir . '/*.php');
    $res = [];
    foreach($pathList as $key => $value) {
        $res[] = basename($value, '.php');
    }
    return $res;
}

//  获取某个控制器的方法名的函数,方法过滤父级Base控制器的方法，只保留自己的
function getActions($className, $base='\app\admin\controller\Base') {
    $methods = get_class_methods(new $className());
    $baseMethods = get_class_methods(new $base());
    $res = array_diff($methods, $baseMethods);
    return $res;
}

// 截取字符串
function interceptStr($str, $max = 60){
    $old_str = $str;
    $str = mb_substr($old_str, 0, $max, 'utf-8');
    if(mb_strlen($old_str, 'utf-8') > $max){
        $str .= '....';
    }

    return $str;
}

// 搜索条件
function search($data, $str){
    $res = [
        [$str, 'between time', [$data['start'],$data['end']]]
    ];

    return $res;
}

// 生成唯一订单号
function random_num($uid = 1){
    $num = substr(uniqid(), 7, 13);
    $str = '';
    for($i = 0; $i < strlen($num); $i++){
        $str .= ord($num[$i]);
    }
    $str = date('Ymd'). $uid . substr($str, 0, 10) .  str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT);

    return $str;
}

// 分类级别处理
function cateList($data, $pid = 0, $level = 1){
    $arr = [];

    foreach($data as $v){
        if($v['pid'] == $pid){
            $v['level'] = $level;
            $arr[] = $v;
            $temp = cateList($data, $v['id'], $level + 1);
            $arr = array_merge($arr, $temp);
        }
    }

    return $arr;
}

// 单个分类级别处理
function cateFind($data, $pid = 0, $id, $level = 1){
    $arr = [];

    foreach($data as $v){
        if($id == $v['id']){
            if($v['pid'] == $pid){
                $v['level'] = $level;
                $arr[] = $v;
                $temp = cateList($data, $v['id'], $level + 1);
                $arr = array_merge($arr, $temp);
            }
        }
    }

    return $arr;
}

// 获取中文字符拼音首字母
function getFirstCharter($str){
    if(empty($str)){return '';}
    $fchar=ord($str{0});
    if($fchar>=ord('A')&&$fchar<=ord('z')) return strtoupper($str{0});
    $s1=iconv('UTF-8','gb2312//TRANSLIT',$str);
    $s2=iconv('gb2312','UTF-8',$s1);
    $s=$s2==$str?$s1:$str;
    $asc=ord($s{0})*256+ord($s{1})-65536;
    if($asc>=-20319&&$asc<=-20284) return 'A';
    if($asc>=-20283&&$asc<=-19776) return 'B';
    if($asc>=-19775&&$asc<=-19219) return 'C';
    if($asc>=-19218&&$asc<=-18711) return 'D';
    if($asc>=-18710&&$asc<=-18527) return 'E';
    if($asc>=-18526&&$asc<=-18240) return 'F';
    if($asc>=-18239&&$asc<=-17923) return 'G';
    if($asc>=-17922&&$asc<=-17418) return 'H';
    if($asc>=-17417&&$asc<=-16475) return 'J';
    if($asc>=-16474&&$asc<=-16213) return 'K';
    if($asc>=-16212&&$asc<=-15641) return 'L';
    if($asc>=-15640&&$asc<=-15166) return 'M';
    if($asc>=-15165&&$asc<=-14923) return 'N';
    if($asc>=-14922&&$asc<=-14915) return 'O';
    if($asc>=-14914&&$asc<=-14631) return 'P';
    if($asc>=-14630&&$asc<=-14150) return 'Q';
    if($asc>=-14149&&$asc<=-14091) return 'R';
    if($asc>=-14090&&$asc<=-13319) return 'S';
    if($asc>=-13318&&$asc<=-12839) return 'T';
    if($asc>=-12838&&$asc<=-12557) return 'W';
    if($asc>=-12556&&$asc<=-11848) return 'X';
    if($asc>=-11847&&$asc<=-11056) return 'Y';
    if($asc>=-11055&&$asc<=-10247) return 'Z';
    return '';
}

// 读取excel的内容
function read_excel($filename)
{
    //设置excel格式
    $reader = PHPExcel_IOFactory::createReader('Excel2007');
    //载入excel文件
    $excel = $reader->load($filename);
    //读取第一张表
    $sheet = $excel->getSheet(0);
    //获取总行数
    $row_num = $sheet->getHighestRow();
    //获取总列数
    $col_num = $sheet->getHighestColumn();

    $data = []; //数组形式获取表格数据
    for($col='A';$col<=$col_num;$col++)
    {
        //从第二行开始，去除表头（若无表头则从第一行开始）
        for($row=2;$row<=$row_num;$row++)
        {
            $data[$row-2][] = $sheet->getCell($col.$row)->getValue();
        }
    }
    return $data;
}

// 获取时分秒
function time2second($seconds){
    $seconds = (int)$seconds;
    if( $seconds<86400 ){//如果不到一天
        $format_time = gmstrftime('%H时%M分%S秒', $seconds);
    }else{
        $time = explode(' ', gmstrftime('%j %H %M %S', $seconds));//Array ( [0] => 04 [1] => 14 [2] => 14 [3] => 35 )
//        $format_time = ($time[0]-1).'天'.$time[1].'时'.$time[2].'分'.$time[3].'秒';
        $format_time = ($time[0]-1).'天'.$time[1].'时';
    }
    return $format_time;
}

/**
 * @param $arr  数组
 * @param $key  键名
 * @return array 去重之后新的数组
 */
function array_unset_tt($arr,$key){
    //建立一个目标数组
    $res = array();
    foreach ($arr as $value) {
        //查看有没有重复项
        if(isset($res[$value[$key]])){
            unset($value[$key]);  //有：销毁
        }else{
            $res[$value[$key]] = $value;
        }
    }

    return array_values($res);
}
