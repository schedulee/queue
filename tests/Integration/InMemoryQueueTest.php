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

use Schedulee\Queue\InMemoryQueue;

class InMemoryQueueTest extends QueueTest
{
    use Performance;

    public function createQueue()
    {
        return new InMemoryQueue();
    }
}
