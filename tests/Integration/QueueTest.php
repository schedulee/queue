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

use Schedulee\Queue\Queue;
use Schedulee\Tests\Queue\Utils;
use Schedulee\Tests\Queue\TimeUtils;

abstract class QueueTest extends \PHPUnit_Framework_TestCase
{
    use Utils;

    /**
     * @var Queue
     */
    protected $queue;

    /**
     * Whether the queue supports an expired ETA or not.
     *
     * @var bool
     */
    protected $supportsExpiredEta = true;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->queue = $this->createQueue();
    }

    public function testImplementQueueInterface()
    {
        $this->assertInstanceOf('Schedulee\Queue\Queue', $this->queue);
    }

    public function testPushPop()
    {
        $this->queue->push('item');

        $this->assertSame('item', $this->queue->pop());
        $this->assertNull($this->queue->pop());
    }

    public function testPopOrder()
    {
        if ($this->supportsExpiredEta) {
            $this->queue->push('item1');
            $this->queue->push('item2', '-1 hour');
        } else {
            $this->queue->push('item1', '+3 seconds');
            $this->queue->push('item2');
        }

        $this->assertSame('item2', $this->queue->pop());
        if (!$this->supportsExpiredEta) {
            sleep(3);
        }
        $this->assertSame('item1', $this->queue->pop());
    }

    public function testPopDelay()
    {
        $eta = time() + 3;

        $this->queue->push('item', $eta);
        $this->assertNull($this->queue->pop());

        TimeUtils::callAt($eta, function () {
            $this->assertSame('item', $this->queue->pop());
        }, !$this->supportsExpiredEta);
    }

    public function testPushWithExpiredEta()
    {
        $this->queue->push('item', time() - 1);
        $this->assertSame('item', $this->queue->pop());
    }

    public function testPushEqualItems()
    {
        $this->queue->push('item');
        $this->queue->push('item');

        $this->assertSame('item', $this->queue->pop());
        $this->assertSame('item', $this->queue->pop());
    }

    public function testCountAndClear()
    {
        $this->assertSame(0, $this->queue->count());

        for ($i = $count = 5; $i; $i--) {
            $this->queue->push('item'.$i);
        }

        $this->assertSame($count, $this->queue->count());

        $this->queue->clear();
        $this->assertSame(0, $this->queue->count());
    }

    /**
     * @dataProvider provideItemsOfSupportedTypes
     */
    public function testSupportItemType($item, $type)
    {
        $this->queue->push($item);

        if (Types::TYPE_BINARY_STRING === $type) {
            // strict comparison
            $this->assertSame($item, $this->queue->pop());
        } else {
            // loose comparison
            $this->assertEquals($item, $this->queue->pop());
        }
    }

    /**
     * @return Queue
     */
    abstract public function createQueue();
}
