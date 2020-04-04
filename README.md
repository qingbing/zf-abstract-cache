# zf-abstract-cache
缓存抽象类

# 简介
- 该library提供缓存的抽象
- 该缓存实现了 psr/simple 缓存的 "\Psr\SimpleCache\CacheInterface" 接口
- 提供了支持多缓存组件的多缓存处理代码块 "\Zf\Cache\Traits\TMultiCache"
- 缓存组件必须实现抽象类中对应的抽象方法，参考 "\Test\Debug\CacheDemo"

# 使用范例
```php
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
```