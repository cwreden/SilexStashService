<?php

namespace CWreden\Component\Silex\Stash\Provider;


use CWreden\Component\Silex\Stash\Exception\CacheAlreadyExistsException;
use CWreden\Component\Silex\Stash\Factory\PoolFactory;
use CWreden\Component\Silex\Stash\Service\StashService;
use Pimple;
use Silex\Application;
use Silex\ServiceProviderInterface;

class StashServiceProvider implements ServiceProviderInterface
{

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Application $app An Application instance
     */
    public function register(Application $app)
    {
        $app['stash.default_options'] = array(
            'driver'   => 'file_system',
            'namespace'   => 'stash'
        );

        $app['stash.cache.register'] = $app->protect(function ($identifier, $options = array()) use ($app) {
            /** @var Pimple $caches */
            $caches = $app['stash.caches'];

            if (isset($caches[$identifier])) {
                throw new CacheAlreadyExistsException("The cache '$identifier' is already registered!");
            }

            $tmp = $app['stash.caches.options'];
            $options = array_replace($app['stash.default_options'], $options);
            $tmp[$identifier] = $options;
            $app['stash.cache.options'] = $tmp;

            $caches[$identifier] = $caches->share(function () use ($options) {
                return PoolFactory::get($options);
            });
        });

        $app['stash.cache.options.initializer'] = $app->protect(function () use ($app) {
            static $initialized = false;
            if ($initialized) {
                return;
            }
            $initialized = true;

            if (!isset($app['stash.caches.options'])) {
                $app['stash.caches.options'] = array(
                    'default' => isset($app['stash.cache.options']) ? $app['stash.cache.options'] : array()
                );
            }

            $tmp = $app['stash.caches.options'];
            foreach ($tmp as $name => &$options) {
                $options = array_replace($app['stash.default_options'], $options);

                if (!isset($app['stash.caches.default'])) {
                    $app['stash.caches.default'] = $name;
                }
            }
            $app['stash.caches.options'] = $tmp;
        });

        $app['stash.caches'] = $app->share(function () use ($app) {
            $app['stash.cache.options.initializer']();

            $caches = new \Pimple();
            foreach ($app['stash.caches.options'] as $name => $options) {

                $caches[$name] = $caches->share(function () use ($options) {
                    return PoolFactory::get($options);
                });
            }
            return $caches;
        });

        $app['stash.cache'] = $app->share(function () use ($app) {
            $caches = $app['stash.caches'];
            return $caches[$app['stash.caches.default']];
        });

        $app['stash'] = $app->share(function () use ($app) {
            return new StashService($app, $app['stash.caches']);
        });
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
        // TODO: Implement boot() method.
    }
}
 