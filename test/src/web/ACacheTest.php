<?php
/**
 * @link        http://www.phpcorner.net
 * @author      qingbing<780042175@qq.com>
 * @copyright   Chengdu Qb Technology Co., Ltd.
 */

namespace Test\Web;


use DebugBootstrap\Abstracts\Tester;
use Test\Debug\CacheDemo;
use Zf\Helper\Exceptions\ClassException;
use Zf\Helper\Object;

/**
 * @author      qingbing<780042175@qq.com>
 * @describe    ACacheTest
 *
 * Class ACacheTest
 * @package Test\Web
 */
class ACacheTest extends Tester
{
    /**
     * @describe    执行函数
     *
     * @throws ClassException
     * @throws \ReflectionException
     * @throws \Zf\Helper\Exceptions\ParameterException
     */
    public function run()
    {
        // 实例化
        $cache = Object::create([
            'class' => CacheDemo::class,
            'namespace' => 'name',
        ]);

        /* @var $cache CacheDemo */
        // 生成一个缓存
        $cache->set('name', 'qingbing');
        $cache->set('sex', 'nan');

        $cache->setMultiple([
            'age' => 19,
            'class' => 1,
            'grade' => 2,
        ]);

        $value = $cache->get('age');
        var_dump($value);

        $data = $cache->getMultiple([
            'name',
            'grade',
            'age',
        ]);
        var_dump($data);

        $cache->delete('age');
        $cache->deleteMultiple(['class', 'name']);

        $has = $cache->has('grade');
        var_dump($has);

        $cache->clear();

        print_r($cache);
    }
}