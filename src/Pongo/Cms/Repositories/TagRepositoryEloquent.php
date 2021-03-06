<?php namespace Pongo\Cms\Repositories;

use Pongo\Cms\Models\Tag as Tag;
use Pongo\Cms\Services\Cache\CacheInterface;

class TagRepositoryEloquent extends BaseRepositoryEloquent implements TagRepositoryInterface {

	/**
	 * @var Cache
	 */
	protected $cache;

	/**
	 * Tag Repository constructor
	 */
	function __construct(Tag $tag, CacheInterface $cache)
	{
		$this->model = $tag;
		$this->cache = $cache;

		// Set cache parameters
		$this->cache->cachekey = 'tags';
		$this->cache->minutes = 10;
	}



}