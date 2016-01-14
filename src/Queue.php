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

interface Queue extends \Countable
{
    /**
     * Adds an item to the queue.
     *
     * @param mixed $item An item to be added.
     * @param mixed $eta  The earliest time that an item can be popped.
     */
    public function push($item, $eta = null);

    /**
     * Removes an item from the queue and returns it.
     *
     * @return mixed|null
     */
    public function pop();

    /**
     * Removes all items from the queue.
     */
    public function clear();
}
