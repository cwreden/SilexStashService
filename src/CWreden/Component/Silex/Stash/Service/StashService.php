<?php

namespace CWreden\Component\Silex\Stash\Service;


use CWreden\Component\Silex\Stash\Exception\CacheNotExistsException;
use Pimple;
use Silex\Application;
use Stash\Pool;

class StashService
{

    /**
     * @var Pimple
     */
    private $caches;

    function __construct(Pimple $caches)
    {
        $this->caches = $caches;
    }

    /**
     * @param $identifier
     * @throws CacheNotExistsException
     * @return Pool
     */
    public function get($identifier)
    {
        if (!isset($this->caches[$identifier])) {
            throw new CacheNotExistsException("Cache '$identifier' not found!");
        }
        return $this->caches[$identifier];
    }

    /**
     * @param $cacheName
     */
    public function setDefaultCache($cacheName)
    {
        $app['stash.caches.default'] = $cacheName;
    }
}
 