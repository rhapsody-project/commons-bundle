<?php
namespace Rhapsody\CommonsBundle\Cache;


class CacheManager
{

	protected $cache = array();
	protected $timeToLive = 3600;

	public function add(CacheInterface $cache)
	{
		$this->cache[] = $cache;
		return $this;
	}

	/**
	 * Clears the cache.
	 */
	public function clear()
	{
		$this->cache = array();
	}

	public function get($key)
	{
		// 1. if key exists, return it otherwise return null
		// 2. when successfully retrieving an entry update its last accessed timestamp
	}

	/**
	 * Purges all expired elements from the cache.
	 */
	public function purge()
	{
		$expiration = new \DateTime;
		$expiration = $expiration - $this->timeToLive;

		$alive = array();
		$alive = array_filter($this->cache, function(CacheInterface &$item) use ($expiration) {
			return $item->getTimestamp() > $expiration;
		});
		$this->cache = $alive;
	}
}