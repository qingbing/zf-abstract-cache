<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Test\Debug;


use Zf\Cache\Abstracts\ACache;
use Zf\Cache\Traits\TMultiCache;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    CacheDemo
 *
 * Class CacheDemo
 * @package Test\Debug
 */
class CacheDemo extends ACache
{
    /**
     * 通用的多缓存管理
     */
    use TMultiCache;

    private $_data = [];

    public function init()
    {
        $this->_data[$this->namespace] = [];
    }

    /**
     * @describe    获取真实的缓存键
     *
     * @param mixed $key
     *
     * @return string
     */
    protected function buildId($key): string
    {
        return md5($key);
    }

    /**
     * @describe    通过缓存id获取信息
     *
     * @param string $id
     *
     * @return mixed
     */
    protected function getValue($id)
    {
        if (!isset($this->_data[$this->namespace][$id])) {
            return null;
        }
        if ($this->_data[$this->namespace][$id][1] < time()) {
            unset($this->_data[$this->namespace][$id]);
            return null;
        }
        return $this->_data[$this->namespace][$id][0];
    }

    /**
     * @describe    设置缓存id的信息
     *
     * @param string $id
     * @param string $value
     * @param int $ttl
     *
     * @return bool
     */
    protected function setValue(string $id, string $value, $ttl): bool
    {
        $this->_data[$this->namespace][$id] = [$value, time() + $ttl];
        return true;
    }

    /**
     * @describe    删除缓存信息
     *
     * @param string $id
     *
     * @return bool
     */
    protected function deleteValue(string $id): bool
    {
        unset($this->_data[$this->namespace][$id]);
        return true;
    }

    /**
     * @describe    清理当前命名空间的缓存
     *
     * @return bool
     */
    protected function clearValues(): bool
    {
        $this->_data[$this->namespace] = [];
        return true;
    }

    /**
     * @describe    判断缓存是否存在
     *
     * @param string $id
     *
     * @return bool
     */
    protected function exist(string $id): bool
    {
        if (!isset($this->_data[$this->namespace][$id])) {
            return false;
        }
        if ($this->_data[$this->namespace][$id][1] < time()) {
            unset($this->_data[$this->namespace][$id]);
            return false;
        }
        return true;
    }
}