<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Zf\Cache\Abstracts;


use Psr\SimpleCache\CacheInterface;
use Zf\Helper\Abstracts\Component;
use Zf\Helper\Business\CryptSecure;
use Zf\Helper\Exceptions\ParameterException;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    缓存抽象
 *
 * Class ACache
 * @package Zf\Cache\Abstracts
 */
abstract class ACache extends Component implements CacheInterface
{
    /**
     * @describe    缓存命名空间
     *
     * @var string
     */
    public $namespace = 'zf';
    /**
     * @describe    缓存默认生效时长
     *
     * @var int
     */
    private $_ttl = 3600;
    /**
     * @describe    缓存是否工作
     *
     * @var bool
     */
    private $_isWorking = true;
    /**
     * @describe    缓存结果是否加密
     *
     * @var bool
     */
    private $_encrypt = false;

    /**
     * @describe    获取缓存是否工作
     *
     * @return bool
     */
    public function getIsWorking(): bool
    {
        return $this->_isWorking;
    }

    /**
     * @describe    设置缓存是否工作
     *
     * @param bool $isWorking
     */
    public function setIsWorking(bool $isWorking)
    {
        $this->_isWorking = $isWorking;
    }

    /**
     * @describe    获取缓存默认生效时长
     *
     * @return int
     */
    public function getTtl(): int
    {
        return $this->_ttl;
    }

    /**
     * @describe    设置缓存默认生效时长
     *
     * @param int $ttl
     */
    public function setTtl(int $ttl)
    {
        $this->_ttl = $ttl;
    }

    /**
     * @describe    获取缓存结果是否加密
     *
     * @return bool
     */
    public function getEncrypt(): bool
    {
        return $this->_encrypt;
    }

    /**
     * @param bool $encrypt
     */
    /**
     * @describe    设置缓存结果是否加密
     *
     * @param bool $encrypt
     */
    public function setEncrypt(bool $encrypt)
    {
        $this->_encrypt = $encrypt;
    }

    /**
     * @describe    编码需要存储的数据信息，memcache支持数组存储
     *
     * @param mixed $value
     *
     * @return mixed
     */
    protected function encodeSaveValue($value)
    {
        return $this->getEncrypt() ? CryptSecure::encode($value) : serialize($value);
    }

    /**
     * @describe    解码读取的数据信息
     *
     * @param mixed $saveValue
     *
     * @return mixed
     */
    protected function decodeSaveValue($saveValue)
    {
        return $this->getEncrypt() ? CryptSecure::decode($saveValue) : unserialize($saveValue);
    }

    /**
     * @describe    获取缓存值
     *
     * @param mixed $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get($key, $default = null)
    {
        if (!$this->getIsWorking()) {
            return $default;
        }

        $id = $this->buildId($key);
        if (null === ($value = $this->getValue($id))) {
            return $default;
        }
        return $this->decodeSaveValue($value);
    }

    /**
     * @describe    设置缓存值
     *
     * @param mixed $key
     * @param mixed $value
     * @param null|int $ttl
     *
     * @return bool
     */
    public function set($key, $value, $ttl = null): bool
    {
        $id = $this->buildId($key);
        $ttl = (null === $ttl) ? $this->getTtl() : $ttl;
        return $this->setValue($id, $this->encodeSaveValue($value), $ttl);
    }

    /**
     * @describe    删除缓存值
     *
     * @param mixed $key
     *
     * @return bool
     */
    public function delete($key)
    {
        $id = $this->buildId($key);
        return $this->deleteValue($id);
    }

    /**
     * @describe    清理命名空间下所有的缓存
     *
     * @return bool
     */
    public function clear(): bool
    {
        return $this->clearValues();
    }

    /**
     * @describe    同时获取多个缓存
     *
     * @param iterable $keys
     * @param mixed $default
     *
     * @return array|iterable
     * @throws ParameterException
     */
    public function getMultiple($keys, $default = null)
    {
        if (!is_array($keys) && !$keys instanceof \Traversable) {
            throw new ParameterException('存取批量操作参数必须可遍历');
        }
        if (empty($keys)) {
            return [];
        }
        $ids = $this->buildIds($keys);
        $data = $this->getMultiValue($ids);
        $R = [];
        foreach ($ids as $key => $id) {
            if (isset($data[$id]) && null !== $data[$id]) {
                $R[$key] = $this->decodeSaveValue($data[$id]);
            } else {
                $R[$key] = $default;
            }
        }
        return $R;
    }

    /**
     * @describe    同时设置多个缓存
     *
     * @param iterable $values
     * @param null|int $ttl
     *
     * @return bool
     * @throws ParameterException
     */
    public function setMultiple($values, $ttl = null)
    {
        if (!is_array($values) && !$values instanceof \Traversable) {
            throw new ParameterException('设置批量操作参数必须可遍历');
        }
        if (empty($values)) {
            return false;
        }
        $ks = [];
        foreach ($values as $key => $value) {
            $ks[$this->buildId($key)] = $this->encodeSaveValue($value);
        }
        $ttl = (null === $ttl) ? $this->getTtl() : $ttl;
        return $this->setMultiValue($ks, $ttl);
    }

    /**
     * @describe    同时删除多个缓存
     *
     * @param iterable $keys
     *
     * @return array|bool
     *
     * @throws ParameterException
     */
    public function deleteMultiple($keys)
    {
        if (!is_array($keys) && !$keys instanceof \Traversable) {
            throw new ParameterException('存取批量操作参数必须可遍历');
        }
        if (empty($keys)) {
            return [];
        }
        $ids = $this->buildIds($keys);
        return $this->deleteMultiValue($ids);
    }

    /**
     * @describe    判断缓存是否存在
     *
     * @param string $key
     *
     * @return bool
     */
    public function has($key)
    {
        $id = $this->buildId($key);
        return $this->exist($id);
    }

    /**
     * @describe    生成多个缓存id
     *
     * @param array $keys
     *
     * @return array
     */
    protected function buildIds(array $keys): array
    {
        $ids = [];
        foreach ($keys as $key) {
            $ids[$key] = $this->buildId($key);
        }
        return $ids;
    }

    /**
     * @describe    获取缓存id
     *
     * @param mixed $key
     *
     * @return string
     */
    abstract protected function buildId($key): string;

    /**
     * @describe    通过缓存id获取信息
     *
     * @param string $id
     *
     * @return mixed
     */
    abstract protected function getValue($id);

    /**
     * @describe    设置缓存id的信息
     *
     * @param string $id
     * @param string $value
     * @param int $ttl
     *
     * @return bool
     */
    abstract protected function setValue(string $id, string $value, $ttl): bool;

    /**
     * @describe    删除缓存信息
     *
     * @param string $id
     *
     * @return bool
     */
    abstract protected function deleteValue(string $id): bool;

    /**
     * @describe    清理当前命名空间的缓存
     *
     * @return bool
     */
    abstract protected function clearValues(): bool;

    /**
     * @describe    通过缓存ids获取信息
     *
     * @param array $ids
     *
     * @return array
     */
    abstract protected function getMultiValue($ids);

    /**
     * @describe    设置多个缓存
     *
     * @param mixed $kvs
     * @param null|int $ttl
     *
     * @return bool
     */
    abstract protected function setMultiValue($kvs, $ttl = null): bool;

    /**
     * @describe    删除多个缓存
     *
     * @param array $ids
     *
     * @return bool
     */
    abstract protected function deleteMultiValue($ids);

    /**
     * @describe    判断缓存是否存在
     *
     * @param string $id
     *
     * @return bool
     */
    abstract protected function exist(string $id): bool;
}