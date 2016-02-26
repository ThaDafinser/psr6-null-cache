<?php
namespace Psr6NullCache\Adapter;

use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use DateTime;
use Psr6NullCache\CacheItem;

final class MemoryCacheItemPool implements CacheItemPoolInterface
{

    /**
     *
     * @var array
     */
    private $data = [];

    private $deferred = [];

    /**
     * Returns a Cache Item representing the specified key.
     *
     * This method must always return a CacheItemInterface object, even in case of
     * a cache miss. It MUST NOT return null.
     *
     * @param string $key
     *            The key for which to return the corresponding Cache Item.
     *            
     * @throws InvalidArgumentException If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *         MUST be thrown.
     *        
     * @return CacheItemInterface
     */
    public function getItem($key)
    {
        if ($this->hasItem($key) !== true) {
            $this->data[$key] = new CacheItem($key, null, false);
        }
        
        return $this->data[$key];
    }

    /**
     * Returns a traversable set of cache items.
     *
     * @param array $keys
     *            An indexed array of keys of items to retrieve.
     *            
     * @throws InvalidArgumentException If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *         MUST be thrown.
     *        
     * @return array|\Traversable A traversable collection of Cache Items keyed by the cache keys of
     *         each item. A Cache item will be returned for each key, even if that
     *         key is not found. However, if no keys are specified then an empty
     *         traversable MUST be returned instead.
     */
    public function getItems(array $keys = [])
    {
        $result = [];
        
        foreach ($keys as $key) {
            $result[$key] = $this->getItem($key);
        }
        
        return $result;
    }

    /**
     * Confirms if the cache contains specified cache item.
     *
     * Note: This method MAY avoid retrieving the cached value for performance reasons.
     * This could result in a race condition with CacheItemInterface::get(). To avoid
     * such situation use CacheItemInterface::isHit() instead.
     *
     * @param string $key
     *            The key for which to check existence.
     *            
     * @throws InvalidArgumentException If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *         MUST be thrown.
     *        
     * @return bool True if item exists in the cache, false otherwise.
     */
    public function hasItem($key)
    {
        if (isset($this->data[$key])) {
            
            /* @var $item \Psr6NullCache\CacheItem */
            $item = $this->data[$key];
            
            if ($item->isHit() === true && ($item->getExpires() === null || $item->getExpires() > new DateTime())) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Deletes all items in the pool.
     *
     * @return bool True if the pool was successfully cleared. False if there was an error.
     */
    public function clear()
    {
        $this->data = [];
        
        return true;
    }

    /**
     * Removes the item from the pool.
     *
     * @param string $key
     *            The key for which to delete
     *            
     * @throws InvalidArgumentException If the $key string is not a legal value a \Psr\Cache\InvalidArgumentException
     *         MUST be thrown.
     *        
     * @return bool True if the item was successfully removed. False if there was an error.
     */
    public function deleteItem($key)
    {
        unset($this->data[$key]);
        
        return true;
    }

    /**
     * Removes multiple items from the pool.
     *
     * @param array $keys
     *            An array of keys that should be removed from the pool.
     *            
     * @throws InvalidArgumentException If any of the keys in $keys are not a legal value a \Psr\Cache\InvalidArgumentException
     *         MUST be thrown.
     *        
     * @return bool True if the items were successfully removed. False if there was an error.
     */
    public function deleteItems(array $keys)
    {
        foreach ($keys as $key) {
            $this->deleteItem($key);
        }
        
        return true;
    }

    /**
     * Persists a cache item immediately.
     *
     * @param CacheItemInterface $item
     *            The cache item to save.
     *            
     * @return bool True if the item was successfully persisted. False if there was an error.
     */
    public function save(CacheItemInterface $item)
    {
        $item->setIsHit(true);
        
        $this->data[$item->getKey()] = $item;
        
        return true;
    }

    /**
     * Sets a cache item to be persisted later.
     *
     * @param CacheItemInterface $item
     *            The cache item to save.
     *            
     * @return bool False if the item could not be queued or if a commit was attempted and failed. True otherwise.
     */
    public function saveDeferred(CacheItemInterface $item)
    {
        $this->deferred[$item->getKey()] = $item;
        
        return true;
    }

    /**
     * Persists any deferred cache items.
     *
     * @return bool True if all not-yet-saved items were successfully saved or there were none. False otherwise.
     */
    public function commit()
    {
        foreach ($this->deferred as $item) {
            /* @var $item \Psr6NullCache\CacheItem */
            $this->save($item);
        }
        
        $this->deferred = [];
        
        return true;
    }
}
