<?php
/**
 * Phossa Project
 *
 * PHP version 5.4
 *
 * @category  Library
 * @package   Phossa2\Route
 * @copyright Copyright (c) 2016 phossa.com
 * @license   http://mit-license.org/ MIT License
 * @link      http://www.phossa.com/
 */
/*# declare(strict_types=1); */

namespace Phossa2\Route\Traits;

use Phossa2\Route\Message\Message;
use Phossa2\Shared\Debug\DebuggableInterface;
use Phossa2\Route\Interfaces\CollectorInterface;
use Phossa2\Route\Interfaces\CollectorAwareInterface;

/**
 * CollectorAwareTrait
 *
 * Implementation of CollectorAwareInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     CollectorAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait CollectorAwareTrait
{
    /**
     * collector pool
     *
     * @var    CollectorInterface[]
     * @access protected
     */
    protected $collectors = [];

    /**
     * {@inheritDoc}
     */
    public function addCollector(CollectorInterface $collector)
    {
        // debugging
        if ($this instanceof DebuggableInterface && $this->isDebugging()) {
            $this->debug(
                Message::get(Message::RTE_COLLECTOR_ADD, get_class($collector))
            );
            $this->delegateDebugger($collector);
        }

        $this->collectors[] = $collector;
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getCollectors()/*# : array */
    {
        return $this->collectors;
    }
}
