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

namespace Phossa2\Route\Collector;

use Phossa2\Route\Route;
use Phossa2\Route\Message\Message;
use Phossa2\Route\Traits\AddRouteTrait;
use Phossa2\Shared\Debug\DebuggableTrait;
use Phossa2\Route\Traits\PrefixAwareTrait;
use Phossa2\Route\Traits\HandlerAwareTrait;
use Phossa2\Shared\Debug\DebuggableInterface;
use Phossa2\Route\Interfaces\ResultInterface;
use Phossa2\Route\Interfaces\CollectorInterface;
use Phossa2\Route\Interfaces\PrefixAwareInterface;
use Phossa2\Route\Interfaces\HandlerAwareInterface;
use Phossa2\Event\EventableExtensionCapableAbstract;

/**
 * CollectorAbstract
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionCapableAbstract
 * @see     CollectorInterface
 * @see     HandlerAwareInterface
 * @see     DebuggableInterface
 * @version 2.0.1
 * @since   2.0.0 added
 * @since   2.0.1 added PrefixAware* stuff
 */
abstract class CollectorAbstract extends EventableExtensionCapableAbstract implements CollectorInterface, PrefixAwareInterface, HandlerAwareInterface, DebuggableInterface
{
    use HandlerAwareTrait, PrefixAwareTrait, DebuggableTrait, AddRouteTrait;

    /**#@+
     * Collector level events
     *
     * @const
     */

    // before match in this collector
    const EVENT_BEFORE_MATCH = 'collector.match.before';

    // after a successful match in this collector
    const EVENT_AFTER_MATCH = 'collector.match.after';

    /**#@-*/

    /**
     * Constructor
     *
     * @param  array $properties
     * @access public
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * {@inheritDoc}
     *
     * @since 2.0.1 added patch prefix checking
     */
    public function matchRoute(ResultInterface $result)/*# : bool */
    {
        // skip if path prefix not match
        if (!$this->matchPathPrefix($result->getPath())) {
            return false;
        }

        // match with each routes in the collector
        return $this->realMatchRoute($result);
    }

    /**
     * Matching
     *
     * @param  ResultInterface $result
     * @return boolean
     * @access protected
     */
    protected function realMatchRoute(ResultInterface $result)/*# : bool */
    {
        $res = false;
        $param = ['result' => $result];
        if ($this->trigger(self::EVENT_BEFORE_MATCH, $param) &&
            $this->match($result) &&
            $this->trigger(self::EVENT_AFTER_MATCH, $param)
        ) {
            $res = true;
        }

        $this->setCollectorHandler($result);
        return $res;
    }

    /**
     * Set collector level handler if result has no handler yet
     *
     * @param  ResultInterface $result
     * @return $this
     * @access protected
     */
    protected function setCollectorHandler(ResultInterface $result)
    {
        $status = $result->getStatus();
        if (is_null($result->getHandler()) &&
            $this->getHandler($status)
        ) {
            // debug message
            $this->debug(Message::get(
                Message::RTE_COLLECTOR_HANDLER,
                $this->getHandler($status),
                get_class($this)
            ));
            $result->setHandler($this->getHandler($status));
        }
        return $this;
    }

    /**
     * Child class must implement this method
     *
     * MUST set $result status and handler in this method
     *
     * @param  ResultInterface $result result object
     * @return bool
     * @access protected
     */
    abstract protected function match(ResultInterface $result)/*# : bool */;
}
