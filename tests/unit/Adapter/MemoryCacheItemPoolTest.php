<?php
namespace Psr6NullCacheTest\Unit\Adapter;

use PHPUnit_Framework_TestCase;
use Psr6NullCache\CacheItem;
use Psr6NullCache\Adapter\MemoryCacheItemPool;

/**
 * @covers Psr6NullCache\Adapter\MemoryCacheItemPool
 */
class MemoryCacheItemPoolTest extends PHPUnit_Framework_TestCase
{

    public function testGetItemNotCached()
    {
        $pool = new MemoryCacheItemPool();
        
        $item = $pool->getItem('myKey');
        
        $this->assertInstanceOf('Psr6NullCache\CacheItem', $item);
        $this->assertEquals('myKey', $item->getKey());
        // not from cache -> new object, so false!
        $this->assertFalse($item->isHit());
        $this->assertNull($item->get());
    }

    public function testGetItems()
    {
        $pool = new MemoryCacheItemPool();
        
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
        $pool = new MemoryCacheItemPool();
        
        // not in $this->data
        $this->assertFalse($pool->hasItem('myKey'));
        
        // isHit() is false (not saved)
        $item = $pool->getItem('myKey');
        $this->assertFalse($pool->hasItem('myKey'));
        
        // saved, so all ok!
        $pool->save($item);
        $this->assertTrue($pool->hasItem('myKey'));
        
        // expired item
        $date = new \DateTime('now -5 seconds');
        $item->expiresAt($date);
        $pool->save($item);
        $this->assertFalse($pool->hasItem('myKey'));
    }

    public function testClear()
    {
        $pool = new MemoryCacheItemPool();
        $this->assertTrue($pool->clear());
        
        $item = $pool->getItem('myKey');
        $pool->save($item);
        $this->assertTrue($pool->hasItem('myKey'));
        $this->assertTrue($pool->clear());
        $this->assertFalse($pool->hasItem('myKey'));
    }

    public function testDeleteItem()
    {
        $pool = new MemoryCacheItemPool();
        $this->assertTrue($pool->deleteItem('not-available-key'));
        
        $item = $pool->getItem('myKey');
        $pool->save($item);
        $this->assertTrue($pool->hasItem('myKey'));
        $this->assertTrue($pool->deleteItem('myKey'));
        $this->assertFalse($pool->hasItem('myKey'));
    }

    public function testDeleteItems()
    {
        $pool = new MemoryCacheItemPool();
        $this->assertTrue($pool->deleteItems([]));
        
        $item = $pool->getItem('myKey');
        $pool->save($item);
        $item = $pool->getItem('myKey2');
        $pool->save($item);
        
        $this->assertTrue($pool->hasItem('myKey'));
        $this->assertTrue($pool->hasItem('myKey2'));
        $this->assertTrue($pool->deleteItems([
            'myKey',
            'myKey2'
        ]));
        $this->assertFalse($pool->hasItem('myKey'));
        $this->assertFalse($pool->hasItem('myKey2'));
    }

    public function testSave()
    {
        $pool = new MemoryCacheItemPool();
        
        $item = $pool->getItem('myKey');
        $this->assertFalse($item->isHit());
        
        $pool->save($item);
        $this->assertTrue($item->isHit());
        $this->assertTrue($pool->hasItem('myKey'));
    }

    public function testDeferred()
    {
        $pool = new MemoryCacheItemPool();
        
        $item = $pool->getItem('myKey');
        
        $pool->saveDeferred($item);
        $this->assertFalse($item->isHit());
        $this->assertFalse($pool->hasItem('myKey'));
        
        $pool->commit();
        $this->assertTrue($item->isHit());
        $this->assertTrue($pool->hasItem('myKey'));
    }
}
