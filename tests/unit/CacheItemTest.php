<?php
namespace Psr6NullCacheTest\Unit;

use PHPUnit_Framework_TestCase;
use Psr6NullCache\CacheItem;

/**
 * @covers Psr6NullCache\CacheItem
 */
class CacheItemTest extends PHPUnit_Framework_TestCase
{

    public function testCreateSimpleCacheItem()
    {
        $item = new CacheItem(123, 'myValue', false);
        
        $this->assertEquals(123, $item->getKey());
        $this->assertEquals('myValue', $item->get());
        $this->assertFalse($item->isHit());
    }

    public function testCreateAnotherCacheItem()
    {
        $item = new CacheItem('id', null, true);
        
        $this->assertEquals('id', $item->getKey());
        $this->assertNull($item->get());
        $this->assertTrue($item->isHit());
    }

    public function testSet()
    {
        $item = new CacheItem('id', 'a value', false);
        
        $this->assertEquals('a value', $item->get());
        
        $item->set('changed value');
        $this->assertEquals('changed value', $item->get());
    }

    public function testExpiresAtFluidInterface()
    {
        $item = new CacheItem(123, 'myValue', false);
        
        $this->assertSame($item, $item->expiresAt('date in the future'));
    }

    public function testExpiresAfterFluidInterface()
    {
        $item = new CacheItem(123, 'myValue', false);
        
        $this->assertSame($item, $item->expiresAfter('date in the future'));
    }
}
