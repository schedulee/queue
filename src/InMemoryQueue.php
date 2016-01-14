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

class InMemoryQueue implements Queue
{
    /**
     * @var \SplPriorityQueue
     */
    private $queue;

    /**
     * @var int
     */
    private $queueOrder;

    public function __construct()
    {
        $this->queue = new \SplPriorityQueue();
        $this->queueOrder = PHP_INT_MAX;
    }

    /**
     * {@inheritdoc}
     */
    public function push($item, $eta = null)
    {
        $eta = QueueUtils::normalizeEta($eta);
        $this->queue->insert($item, [-$eta, $this->queueOrder--]);
    }

    /**
     * {@inheritdoc}
     */
    public function pop()
    {
        if ($this->queue->isEmpty()) {
            return;
        }

        $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_PRIORITY);
        $priority = $this->queue->top();

        if (time() + $priority[0] >= 0) {
            $this->queue->setExtractFlags(\SplPriorityQueue::EXTR_DATA);

            return $this->queue->extract();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function count()
    {
        return $this->queue->count();
    }

    /**
     * {@inheritdoc}
     */
    public function clear()
    {
        $this->queue = new \SplPriorityQueue();
        $this->queueOrder = PHP_INT_MAX;
    }
}
