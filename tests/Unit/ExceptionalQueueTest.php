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

use Schedulee\Queue\ExceptionalQueue;
use Schedulee\Queue\QueueException;
use Schedulee\Tests\Queue\Utils;

class ExceptionalQueueTest extends \PHPUnit_Framework_TestCase
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
        $this->queue = new ExceptionalQueue($this->innerQueue);
    }

    public function testImplementQueueInterface()
    {
        $this->assertInstanceOf('Schedulee\Queue\Queue', $this->queue);
    }

    public function testImplementQueueAwareInterface()
    {
        $this->assertInstanceOf('Schedulee\Queue\QueueAware', $this->queue);
    }

    public function testPush()
    {
        $item = 'foo';

        $this->innerQueue->expects($this->once())->method('push')
            ->with($this->equalTo($item));

        $this->queue->push($item);
    }

    public function testPop()
    {
        $this->innerQueue->expects($this->once())->method('pop');
        $this->queue->pop();
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

    /**
     * @dataProvider provideQueueInterfaceMethods
     * @expectedException \Schedulee\Queue\QueueException
     */
    public function testThrowOriginalQueueException($method)
    {
        $this->innerQueue->expects($this->once())->method($method)
            ->will($this->throwException(new QueueException($this->innerQueue)));

        $this->callQueueMethod($this->queue, $method);
    }

    /**
     * @dataProvider provideQueueInterfaceMethods
     * @expectedException \Schedulee\Queue\QueueException
     */
    public function testThrowWrappedQueueException($method)
    {
        $this->innerQueue->expects($this->once())->method($method)
            ->will($this->throwException(new \Exception()));

        $this->callQueueMethod($this->queue, $method);
    }

    public function testGetQueue()
    {
        $this->assertSame($this->innerQueue, $this->queue->getQueue());
    }
}
