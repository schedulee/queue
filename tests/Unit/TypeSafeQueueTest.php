<?php

/*
 * This file is part of the schedulee/schedulee package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schedulee\Tests\Queue\Unit;

use Schedulee\Queue\TypeSafeQueue;
use Schedulee\Tests\Queue\Utils;

class TypeSafeQueueTest extends \PHPUnit_Framework_TestCase
{
    use Utils;

    private $innerQueue;
    private $queue;

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->innerQueue = $this->getQueueMock();
        $this->queue = new TypeSafeQueue($this->innerQueue);
    }

    public function testImplementQueueInterface()
    {
        $this->assertInstanceOf('Schedulee\Queue\Queue', $this->queue);
    }

    public function testImplementQueueAwareInterface()
    {
        $this->assertInstanceOf('Schedulee\Queue\QueueAware', $this->queue);
    }

    /**
     * @dataProvider provideItemsOfSupportedTypes
     */
    public function testPush($item)
    {
        $serializedItem = null;

        $this->innerQueue->expects($this->once())->method('push')
            ->with($this->callback(function ($subject) use (&$serializedItem) {
                $serializedItem = $subject;

                return is_string($subject) && ctype_print($subject);
            }));

        $this->queue->push($item);

        return ['original' => $item, 'serialized' => $serializedItem];
    }

    /**
     * @depends testPush
     */
    public function testPop($data)
    {
        $this->innerQueue->expects($this->once())->method('pop')
            ->will($this->returnValue($data['serialized']));

        $this->assertEquals($data['original'], $this->queue->pop());
    }

    public function testCount()
    {
        $this->innerQueue->expects($this->once())->method('count')
            ->will($this->returnValue(42));

        $this->assertSame(42, $this->queue->count());
    }

    public function testClear()
    {
        $this->innerQueue->expects($this->once())->method('clear');
        $this->queue->clear();
    }

    public function testGetQueue()
    {
        $this->assertSame($this->innerQueue, $this->queue->getQueue());
    }
}
