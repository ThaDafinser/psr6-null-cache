
# PSR-6 NullObject cache

[![Build Status](https://travis-ci.org/ThaDafinser/psr6-null-cache.svg)](https://travis-ci.org/ThaDafinser/psr6-null-cache)
[![Code Coverage](https://scrutinizer-ci.com/g/ThaDafinser/psr6-null-cache/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ThaDafinser/psr6-null-cache/?branch=master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ThaDafinser/psr6-null-cache/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ThaDafinser/psr6-null-cache/?branch=master)
[![PHP 7 ready](http://php7ready.timesplinter.ch/ThaDafinser/psr6-null-cache/badge.svg)](https://travis-ci.org/ThaDafinser/psr6-null-cache)

[![Latest Stable Version](https://poser.pugx.org/thadafinser/psr6-null-cache/v/stable)](https://packagist.org/packages/thadafinser/psr6-null-cache)
[![Latest Unstable Version](https://poser.pugx.org/thadafinser/psr6-null-cache/v/unstable)](https://packagist.org/packages/thadafinser/psr6-null-cache) 
[![License](https://poser.pugx.org/thadafinser/psr6-null-cache/license)](https://packagist.org/packages/thadafinser/psr6-null-cache)
[![Total Downloads](https://poser.pugx.org/thadafinser/psr6-null-cache/downloads)](https://packagist.org/packages/thadafinser/psr6-null-cache) 

The missing PSR-6 NullObject implementation.

You can use this package, when you want to
 - avoid using `null` check logic, read more [here](http://designpatternsphp.readthedocs.org/en/latest/Behavioral/NullObject/README.html)
 - need a fake cache implementation for testing
 
## Install

```
composer require thadafinser/psr6-null-cache
```

## Example / usage

Before this package, you needed to allow `null` as a parameter, if you wanted to avoid a package dependency to a specific `PSR-6 cache implementation`


### Old code

```php
namespace MyPackage;

use Psr\Cache\CacheItemPoolInterface;

class MyCode
{

    public function __construct(CacheItemPoolInterface $cache = null)
    {
        $this->cache = $cache;
    }

    /**
     * Can return an instance of null, which is bad!
     *
     * @return null CacheItemPoolInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    private function internalHeavyMethod()
    {
        $cacheKey = 'myKey';
        
        // you need to check first, if there is a cache instance around
        if ($this->getCache() !== null && $this->getCache()->hasItem($cacheKey) === true) {
            // cache is available + it has a cache hit!
            return $this->getCache()->getItem($cacheKey);
        }
        
        $result = do_something_heavy();
        
        // you need to check first, if there is a cache instance around
        if ($this->getCache() !== null) {
            $item = $this->getCache()->getItem($cacheKey);
            $item->set($result);
            $this->getCache()->save($item);
        }
        
        return $result;
    }
}

```

### New code

```php
namespace MyPackage;

use Psr\Cache\CacheItemPoolInterface;
use Psr6NullCache\NullCacheItemPool;

class MyCode
{

    /**
     * You could require a cache instance, so you can remove the null check in __construct() as well
     * 
     * @param CacheItemPoolInterface $cache
     */
    public function __construct(CacheItemPoolInterface $cache = null)
    {
        if($cache === null){
            $cache = new NullCacheItemPool();
        }
        
        $this->cache = $cache;
    }

    /**
     * @return CacheItemPoolInterface
     */
    public function getCache()
    {
        return $this->cache;
    }

    private function internalHeavyMethod()
    {
        $cacheKey = 'myKey';
        
        if ($this->getCache()->hasItem($cacheKey) === true) {
            // cache is available + it has a cache hit!
            return $this->getCache()->getItem($cacheKey);
        }
        
        $result = do_something_heavy();
        
        $item = $this->getCache()->getItem($cacheKey);
        $item->set($result);
        $this->getCache()->save($item);
        
        return $result;
    }
}
```
