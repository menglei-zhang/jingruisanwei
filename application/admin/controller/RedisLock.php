<?php
/**
 * Created by PhpStorm.
 * User: Rain
 * Date: 2019/3/1
 * Time: 13:41
 */

namespace app\admin\controller;

use think\Cache;

class RedisLock
{
    /**
     * 单据锁redis key模板
     */
    const REDIS_LOCK_KEY_TEMPLATE = 'lock_%s';

    /**
     * 单据锁默认超时时间（秒）
     */
    const REDIS_LOCK_DEFAULT_EXPIRE_TIME = 300;

    /**
     * 加单据锁
     * @param int $intData 唯一值
     * @param int $intExpireTime 锁过期时间（秒）
     * @return bool|int 加锁成功返回唯一锁ID，加锁失败返回false
     */
    public function addLock($intData, $intExpireTime = self::REDIS_LOCK_DEFAULT_EXPIRE_TIME)
    {
        //参数校验
        if (empty($intData) || $intExpireTime <= 0) {
            return echoArr(0, '参数初始化失败');
        }

        //获取Redis连接
        $objRedisConn = self::getRedisConn();
        if(!$objRedisConn) return echoArr(0, 'Redis 不支持');

        //生成唯一锁ID，解锁需持有此ID
        $intUniqueLockId = self::generateUniqueLockId();

        //根据模板，结合唯一值，生成唯一Redis key（一般来说，唯一值在业务中系统中唯一的）
        $strKey = sprintf(self::REDIS_LOCK_KEY_TEMPLATE, $intData);

        //加锁（通过Redis setnx指令实现，从Redis 2.6.12开始，通过set指令可选参数也可以实现setnx，同时可原子化地设置超时时间）
        $bolRes = $objRedisConn->set($strKey, $intUniqueLockId, ['nx', 'ex'=>$intExpireTime]);

        //加锁成功返回锁ID，加锁失败返回false
        return $bolRes ? echoArr(1, '加锁成功', ['lockId' => $intUniqueLockId]) : echoArr(0, '加锁失败');
    }

    /**
     * 解单据锁
     * @param int $intData 唯一值
     * @param int $intLockId 锁唯一ID
     * @return bool
     */
    public function releaseLock($intData, $intLockId)
    {
        //参数校验
        if (empty($intData) || empty($intLockId)) {
            return false;
        }

        //获取Redis连接
        $objRedisConn = self::getRedisConn();

        //生成Redis key
        $strKey = sprintf(self::REDIS_LOCK_KEY_TEMPLATE, $intData);

        //监听Redis key防止在【比对lock id】与【解锁事务执行过程中】被修改或删除，提交事务后会自动取消监控，其他情况需手动解除监控
        $objRedisConn->watch($strKey);
        if ($intLockId == $objRedisConn->get($strKey)) {
            $objRedisConn->multi()->del($strKey)->exec();
            return true;
        }
        $objRedisConn->unwatch();
        return false;
    }

    /**
     * 获取Redis连接
     */
    private static function getRedisConn()
    {
        $redis = Cache::store('redis') -> handler();

        return $redis;
    }

    /**
     * 用于生成唯一的锁ID的redis key
     */
    const REDIS_LOCK_UNIQUE_ID_KEY = 'lock_unique_id';

    /**
     * 生成锁唯一ID（通过Redis incr指令实现简易版本，可结合日期、时间戳、取余、字符串填充、随机数等函数，生成指定位数唯一ID）
     * @return mixed
     */
    private static function generateUniqueLockId()
    {
        return self::getRedisConn()->incr(self::REDIS_LOCK_UNIQUE_ID_KEY);
    }
}