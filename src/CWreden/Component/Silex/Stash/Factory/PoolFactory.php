<?php

namespace CWreden\Component\Silex\Stash\Factory;


use CWreden\Component\Silex\Stash\Exception\InvalidItemClassException;
use CWreden\Component\Silex\Stash\Exception\InvalidLoggerClassException;
use CWreden\Component\Silex\Stash\Exception\InvalidPoolClassException;
use Stash\Pool;

class PoolFactory
{
    const POOL_CLASS = 'poolClass';
    const ITEM_CLASS = 'itemClass';
    const LOGGER_CLASS = 'loggerClass';

    /**
     * @param array $options
     * @return Pool
     */
    public static function get(array $options = array())
    {
        self::checkOptions($options);
        $className = 'Stash\Pool';
        if (isset($options[self::POOL_CLASS])) {
            $className = $options[self::POOL_CLASS];
        }
        $driver = DriverFactory::get($options);
        /** @var Pool $pool */
        $pool = new $className($driver);
        if (isset($options[self::ITEM_CLASS])) {
            $pool->setItemClass($options[self::ITEM_CLASS]);
        }
        if (isset($options[self::LOGGER_CLASS])) {
            $pool->setLogger($options[self::LOGGER_CLASS]);
        }
        if (isset($options['namespace'])) {
            $pool->setNamespace($options['namespace']);
        }
        return $pool;
    }

    /**
     * @param array $options
     * @throws \CWreden\Component\Silex\Stash\Exception\InvalidLoggerClassException
     * @throws \CWreden\Component\Silex\Stash\Exception\InvalidPoolClassException
     * @throws \CWreden\Component\Silex\Stash\Exception\InvalidItemClassException
     */
    public static function checkOptions(array $options)
    {
        if (isset($options[self::POOL_CLASS])
            && !in_array('Stash\Interfaces\ItemInterface', class_implements($options[self::POOL_CLASS]))
        ) {
            throw new InvalidPoolClassException("The given 'poolClass' " . $options[self::POOL_CLASS] .
                " has to implement the " .
                "\Stash\Interfaces\PoolInterface interface.");
        }
        if (isset($options[self::ITEM_CLASS])
            && !in_array('Stash\Interfaces\ItemInterface', class_implements($options[self::ITEM_CLASS]))
        ) {
            throw new InvalidItemClassException("The given 'poolClass' " . $options[self::ITEM_CLASS] .
                " has to implement the " .
                "\Stash\Interfaces\ItemInterface interface.");
        }

        if (isset($options[self::LOGGER_CLASS])
            && !in_array('PSR\Log\LoggerInterface', class_implements($options[self::LOGGER_CLASS]))
        ) {
            throw new InvalidLoggerClassException("The given 'poolClass' " . $options[self::LOGGER_CLASS] .
                " has to implement the " .
                "\PSR\Log\LoggerInterface interface.");
        }
    }
}
 