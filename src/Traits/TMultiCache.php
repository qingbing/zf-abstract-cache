<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\Cache\Traits;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    缓存批量管理代码块
 *
 * Trait TMultiCache
 * @package Zf\Cache\Traits
 */
trait TMultiCache
{
    /**
     * @describe    通过缓存id获取信息
     *
     * @param array $ids
     *
     * @return array
     */
    protected function getMultiValue($ids)
    {
        $R = [];
        foreach ($ids as $id) {
            $R[$id] = $this->getValue($id);
        }
        return $R;
    }

    /**
     * @describe    设置多个缓存
     *
     * @param mixed $kvs
     * @param null|int $ttl
     *
     * @return bool
     */
    protected function setMultiValue($kvs, $ttl = null): bool
    {
        foreach ($kvs as $id => $value) {
            $this->setValue($id, $value, $ttl);
        }
        return true;
    }

    /**
     * @describe    删除多个缓存
     *
     * @param array $ids
     *
     * @return bool
     */
    protected function deleteMultiValue($ids)
    {
        foreach ($ids as $id) {
            $this->deleteValue($id);
        }
        return true;
    }
}