<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/5/24
 * Time: 16:35
 */

namespace app\admin\controller;

use think\Controller;
use app\admin\validate\Upload as Vali;

class Upload extends Controller
{
  	// 唯一标识
  	private $tempName;
  
    // 存储的文件名称
    private $fileName;

    // 临时文件后缀
    private $ext = 'yu';

    // 上传目录
    private $dir;

    // 目录临时名称
    private $tempDir;

    // 文件总数
    private $totalBlockNum;

    // 当前块的位置
    private $currentBlockNum;

    // 文件资源
    private $file;

    // 最终文件返回路径
    private $path;

    // 锁
    private $lock;

    // 锁id
    private $lockId;

    /**
     * 设置参数
     */
    private function setParam(){
        // 数据
        $data = input();

        // 验证参数
        $vali = new Vali();
        if(!$vali -> scene('upload') -> check($data)){
            return echoArr(0, $vali->getError());
        }

        // 初始化参数
        $this -> currentBlockNum = $data['index'];
        $this -> totalBlockNum = $data['totalBockNum'];

        // 存储目录
        $date = date('Ymd');
        $uploadPath = config('imgRoute') . $date . '/';
        $this -> dir = ROOT_PATH . 'public' . $uploadPath;

        // 文件返回路径
        $this -> path = $uploadPath;

        // 文件临时目录，临时文件名称
        $this -> tempDir = 'temp/' . md5($data['tempName']);

        // 文件资源
        $this -> file = input('file.file');

        // 判断文件是否存在，另起名称
        $i = 0;
      
      	$this -> tempName = $data['tempName'];

        // 防止同一文件命名冲突
        do{
            // 新文件名
            $tempName = pathinfo($data['fileName']);

            // 文件名加密
            $fileName = $tempName['filename'];
            if($i){
                $fileName .= "_$i";
            }
            $fileName = md5($fileName);
            $fileName .= '.' . $tempName['extension'];

            $i++;
        }while (file_exists($this -> dir . $fileName));
        $this -> fileName = $fileName;

        // 文件锁
        $this -> lock = new RedisLock();

        return echoArr(1, '初始化参数成功');
    }

    /**
     * 处理上传数据
     */
    public function maxFile(){
        // 初始化参数
        $result = $this -> setParam();
        if(!$result['code']) return $this -> error($result['msg']);

        // 检测文件资源
        $file = $this -> file;
        if(!$file) return $this -> error('文件资源错误');

        // 上传文件
        $result = $this -> uploadFile();
        if(!$result['code']) return $this -> error($result['msg']);

        // 判断所有文件块是否上传完毕
        $result = $this -> isFileBlock();
        if($result['code']){
            // Redis 加锁
            $result = $this -> redisLock(1);
            if(!$result['code']) return $this -> error($result['msg']);

            // 合并文件块
            $result = $this -> mergeFile();
            if(!$result['code']) return $this -> error($result['msg']);

            // 清除文件块
            $this -> removeBlock();

            // 解除此锁
            $this -> redisLock(0);

            return $this -> success($result['msg'], '', ['path' => $result['data']['path'], 'code' => 2, 'fileName' => $this -> tempName]);
        }

        return $this -> success($result['msg'], '');
    }

    /**
     * Redis 锁
     * @param $lock     0 解锁 / 1 加锁
     * @return mixed
     */
    private function redisLock($lock){
        if(0 == $lock){
            // 解锁
            $this -> lock -> releaseLock($this -> fileName, $this -> lockId);
        } else if(1 == $lock){
            // 加锁，60秒过期
            $result = $this -> lock -> addLock($this -> fileName, 60);
            if($result['code']){
                $this -> lockId = $result['data']['lockId'];
            }

            return $result;
        }
    }

    /**
     * 上传文件
     */
    private function uploadFile(){
        $file = $this -> file;

        // 存储文件
        $newRoute = $this -> dir . $this -> tempDir;
        $info = $file -> rule('date') -> validate(['size' => '30000000', 'ext'=> 'jpg,png,mp4,zip,yu']) -> move($newRoute, '');

        if($info){
            // 输出 20160820/42a79759f284b767dfcb2a0197904287.jpg

            // Windows 转换反斜杠
            $path = $info->getSaveName();
            if("WIN" == substr(PHP_OS,0,3)){
                $path = str_replace('\\', '/', $path);
            }

            return echoArr(1, '上传成功', ['path' => $path]);
        }else{
            // 上传失败获取错误信息
            return echoArr(0, $file->getError());
        }
    }

    /**
     * 合并数据
     */
    private function mergeFile(){
        $dir = $this -> dir . $this -> tempDir . DIRECTORY_SEPARATOR;

        // 判断文件是否存在
        $file = $dir . $this -> fileName;
        if(file_exists($file)) return echoArr(0, '文件已存在');

        // 创建空白文件
        if(is_writable($file)) return echoArr(0, '文件已锁');
        $createFile = fopen($file, 'w+');
        flock($createFile, LOCK_EX);
        if(!$createFile) return echoArr(0, '创建文件失败');
        fclose($createFile);

        // 文件排序
        $unprocessedFileList = glob($dir . '*.' . $this -> ext);
        $fileList = $this -> sortFile($unprocessedFileList);

        // 合并文件
        $index = 0;
        $max = count($fileList);
        while($index < $max){
            $tempStr = file_get_contents($fileList[$index]);
            file_put_contents($file, $tempStr, FILE_APPEND | LOCK_EX);

            $index++;
        };

        // 移动文件
        rename($file, $this -> dir . $this -> fileName);

        return echoArr(1, '合并成功', ['path' => $this -> path . $this -> fileName]);
    }

    /**
     * 清除块
     */
    private function removeBlock(){
        $dir = $this -> dir . $this -> tempDir . DIRECTORY_SEPARATOR;

        // 删除文件
        $fileList = glob($dir . '*.' . $this -> ext);
        foreach($fileList as $v){
            @unlink($v);
        }

        // 删除空文件夹
        @rmdir($dir);
    }

    /**
     * 判断所有文件块是否上传完毕
     */
    private function isFileBlock(){
        // 文件块总数
        $totalNum = $this -> totalBlockNum;

        // 获得的所有文件块
        $dir = $this -> dir . $this -> tempDir . DIRECTORY_SEPARATOR;

        // 判断全部文件块是否上传完毕
        for($i = 0; $i <= $totalNum; $i++){
            $result = glob($dir . "*_$i." . $this -> ext);

            if(!$result) return echoArr(0, '文件未上传');
        }

        return echoArr(1, '文件全部上传');
    }

    /**
     * 文件重新顺序
     *
     * @param $fileList     文件路径列表
     * @return array
     */
    private function sortFile($fileList){
        $list = [];

        foreach($fileList as $v){
            $temp = pathinfo($v)['filename'];

            // 获取下标
            $key = substr($temp, strpos($temp, '_') + 1);
            $list[$key] = $v;
        }

        ksort($list);
        return $list;
    }
}