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
        $this->assertFalse($item->isHit());
        // because it's no hit, remove the value!
        $this->assertNull($item->get());
    }

    public function testCreateAnotherCacheItem()
    {
        $item = new CacheItem('id', 'myValue', true);
        
        $this->assertEquals('id', $item->getKey());
        $this->assertTrue($item->isHit());
        $this->assertEquals('myValue', $item->get());
    }

    public function testSetIsHit()
    {
        $item = new CacheItem('id', 'a value', false);
    
        $this->assertFalse($item->isHit());
        
        $item->setIsHit(true);
        $this->assertTrue($item->isHit());
    }
    
    public function testSet()
    {
        $item = new CacheItem('id', 'a value', true);
        
        $this->assertEquals('a value', $item->get());
        
        $item->set('changed value');
        $this->assertEquals('changed value', $item->get());
    }

    public function testExpiresAt()
    {
        $item = new CacheItem(123, 'myValue', false);
        $this->assertNull($item->getExpires());
        
        $this->assertSame($item, $item->expiresAt('date in the future'));
        $this->assertNull($item->getExpires());
        
        // valid expiration
        $myDate = new \DateTime();
        $item->expiresAt($myDate);
        $this->assertSame($myDate, $item->getExpires());
    }

    public function testExpiresAfterFluidInterface()
    {
        $item = new CacheItem(123, 'myValue', false);
        $this->assertNull($item->getExpires());
        
        $this->assertSame($item, $item->expiresAfter('date in the future'));
        $this->assertNull($item->getExpires());
        
        // add 5 seconds
        $myDate = new \DateTime();
        $item->expiresAfter(5);
        $this->assertGreaterThan($myDate, $item->getExpires());
        
        // add 3 months
        $interval = new \DateInterval('P3M');
        $item->expiresAfter($interval);
        $this->assertGreaterThan($myDate, $item->getExpires());
    }
}
