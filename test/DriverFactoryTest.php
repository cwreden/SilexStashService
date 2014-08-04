<?php

namespace Test;

use CWreden\Component\Silex\Stash\Factory\DriverFactory;
use PHPUnit_Framework_TestCase;

class DriverFactoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * @var DriverFactory
     */
    protected $factory;

    public function setUp()
    {
        $this->factory = new DriverFactory();
    }

    public function testDriverClass()
    {
        $driver = $this->factory->get(array(
            'driver' => 'notExistsDriver'
        ));
        $this->assertInstanceOf('Stash\Interfaces\DriverInterface', $driver, 'Invalid driver class!');
    }
}
