SilexStashService
=================

The stash cache service for silex.


### Services

- stash => StashService
- stash.cache => Default Pool
- stash.caches => Pimple with all registered Pool classes



### Register a new cache pool on runtime

You can register a new cache by using the 'stash.cache.register'-function.

<pre><code>
$app['stash.cache.register']('myNewCachePool', array(
    'driver' => 'apc'
));
</code></pre>


### Possible drivers

- memory
- file_system
- redis
- sqlite
- apc



### Options

<pre><code>
array(
    'driver' => 'apc',            // use the driver oder driverClass option
    'driverClass' => 'My\Driver',
    'poolClass' => 'My\Pool',
    'itemClass' => 'My\Item',
    'loggerClass' => 'My\PSR\Logger'
    ...                           // all stash known options
);
</code></pre>