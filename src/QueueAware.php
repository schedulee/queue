<?php

/*
 * This file is part of the schedulee/schedulee package.
 *
 * (c) Eugene Leonovich <gen.work@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Schedulee\Queue;

interface QueueAware
{
    /**
     * Retrieves the queue instance.
     *
     * @return Queue
     */
    public function getQueue();
}
