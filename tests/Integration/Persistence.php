<?php

/*
 * This file is part of the schedulee/schedulee package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schedulee\Tests\Queue\Integration;

trait Persistence
{
    /**
     * @var \Schedulee\Tests\Queue\Handler
     */
    private static $handler;

    protected function setUp()
    {
        parent::setUp();

        self::getHandler()->clear();
    }

    /**
     * @return \Schedulee\Queue\Queue
     */
    public function createQueue()
    {
        return self::getHandler()->createQueue();
    }

    /**
     * Abstract static class functions are not supported since v5.2.
     *
     * @param array $config
     *
     * @return \Schedulee\Tests\Queue\Handler
     *
     * @throws \BadMethodCallException
     */
    public static function createHandler(array $config)
    {
        throw new \BadMethodCallException(
            sprintf('Method %s:%s is not implemented.', get_called_class(), __FUNCTION__)
        );
    }

    public static function getHandler()
    {
        if (!self::$handler) {
            self::$handler = static::createHandler($_ENV);
        }

        return self::$handler;
    }

    public static function setUpBeforeClass()
    {
        parent::setUpBeforeClass();

        self::getHandler()->reset();
    }

    public static function tearDownAfterClass()
    {
        parent::tearDownAfterClass();

        self::$handler = null;
    }
}
