<?php
namespace Psr6NullCacheTest\Unit\Adapter;

use PHPUnit_Framework_TestCase;
use Psr6NullCache\CacheItem;
use Psr6NullCache\Adapter\NullCacheItemPool;

/**
 * @covers Psr6NullCache\Adapter\NullCacheItemPool
 */
class NullCacheItemPoolTest extends PHPUnit_Framework_TestCase
{

    public function testGetItem()
    {
        $pool = new NullCacheItemPool();
        
        $item = $pool->getItem('myKey');
        
        $this->assertInstanceOf('Psr6NullCache\CacheItem', $item);
        $this->assertEquals('myKey', $item->getKey());
        // not from cache -> new object, so false!
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testGetItems()
    {
        $pool = new NullCacheItemPool();
        
        $items = $pool->getItems([
            'myKey',
            'myKey2'
        ]);
        
        $this->assertInternalType('array', $items);
        $this->assertCount(2, $items);
        
        $this->assertArrayHasKey('myKey', $items);
        $this->assertArrayHasKey('myKey2', $items);
        
        $this->assertInstanceOf('Psr6NullCache\CacheItem', $items['myKey']);
        $this->assertInstanceOf('Psr6NullCache\CacheItem', $items['myKey2']);
    }

    public function testHasItem()
    {
        $pool = new NullCacheItemPool();
        
        $this->assertFalse($pool->hasItem('myKey'));
    }

    public function testClear()
    {
        $pool = new NullCacheItemPool();
        
        $this->assertTrue($pool->clear());
    }

    public function testDeleteItem()
    {
        $pool = new NullCacheItemPool();
        
        $this->assertTrue($pool->deleteItem('myKey'));
    }

    public function testDeleteItems()
    {
        $pool = new NullCacheItemPool();
        
        $this->assertTrue($pool->deleteItems([]));
        
        $this->assertTrue($pool->deleteItems([
            'myKey',
            'myKey2'
        ]));
    }

    public function testSave()
    {
        $pool = new NullCacheItemPool();
        
        $item = new CacheItem('myKey', null, false);
        
        $this->assertTrue($pool->save($item));
    }

    public function testSaveDeferred()
    {
        $pool = new NullCacheItemPool();
        
        $item = new CacheItem('myKey', null, false);
        
        $this->assertTrue($pool->saveDeferred($item));
    }

    public function testCommit()
    {
        $pool = new NullCacheItemPool();
        
        $this->assertTrue($pool->commit());
    }
}
