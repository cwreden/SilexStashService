<?php

namespace CWreden\Component\Silex\Stash\Factory;


use CWreden\Component\Silex\Stash\Exception\DriverRequiredException;
use CWreden\Component\Silex\Stash\Exception\InvalidDriverClassException;
use CWreden\Component\Silex\Stash\Exception\UnknownDriverException;
use Stash\Interfaces\DriverInterface;

class DriverFactory
{
    private static $_driverMap = array(
        'memory' => 'Stash\Driver\Memcache',
        'file_system' => 'Stash\Driver\FileSystem',
        'redis' => 'Stash\Driver\Redis',
        'sqlite' => 'Stash\Driver\Sqlite',
        'apc' => 'Stash\Driver\Apc',
    );

    /**
     * @param array $params
     * @return DriverInterface
     */
    public static function get(array $params)
    {
        self::checkParams($params);
        if (isset($params['driverClass'])) {
            $className = $params['driverClass'];
        } else {
            $className = self::$_driverMap[$params['driver']];
        }
        /** @var DriverInterface $driver */
        $driver = new $className();
        $driver->setOptions($params);
        return $driver;
    }

    /**
     * @param array $params
     * @throws \CWreden\Component\Silex\Stash\Exception\UnknownDriverException
     * @throws \CWreden\Component\Silex\Stash\Exception\InvalidDriverClassException
     * @throws \CWreden\Component\Silex\Stash\Exception\DriverRequiredException
     */
    private static function checkParams(array $params)
    {
        if ( ! isset($params['driver']) && ! isset($params['driverClass'])) {
            throw new DriverRequiredException("The options 'driver' or 'driverClass' are mandatory.");
        }

        if ( isset($params['driver']) && ! isset(self::$_driverMap[$params['driver']])) {
            throw new UnknownDriverException("The given 'driver' " . $params['driver'] . " is unknown, ".
                "Stash currently supports only the following drivers: " .
                implode(", ", array_keys(self::$_driverMap)));
        }

        if (isset($params['driverClass'])
            && ! in_array('Stash\Interfaces\DriverInterface', class_implements($params['driverClass'], true))
        ) {
            throw new InvalidDriverClassException("The given 'driverClass' " . $params['driverClass'] .
                " has to implement the " .
                "\Stash\Interfaces\DriverInterface interface.");
        }
    }
}
 