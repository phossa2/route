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

namespace Phossa2\Route;

use Phossa2\Route\Traits\AddRouteTrait;
use Phossa2\Event\EventCapableAbstract;
use Phossa2\Shared\Debug\DebuggableTrait;
use Phossa2\Route\Traits\HandlerAwareTrait;
use Phossa2\Route\Traits\ResolverAwareTrait;
use Phossa2\Route\Interfaces\RouteInterface;
use Phossa2\Route\Traits\CollectorAwareTrait;
use Phossa2\Shared\Debug\DebuggableInterface;
use Phossa2\Route\Interfaces\ResolverInterface;
use Phossa2\Route\Interfaces\AddRouteInterface;
use Phossa2\Route\Interfaces\CollectorInterface;
use Phossa2\Route\Interfaces\DispatcherInterface;
use Phossa2\Route\Interfaces\HandlerAwareInterface;
use Phossa2\Route\Interfaces\ResolverAwareInterface;
use Phossa2\Route\Interfaces\CollectorAwareInterface;
use Phossa2\Route\Collector\Collector;
use Phossa2\Route\Interfaces\ResultInterface;

/**
 * Dispatcher
 *
 * Matching or dispatching base on the URI PATH and HTTP METHOD
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventCapableAbstract
 * @see     DispatcherInterface
 * @see     HandlerAwareInterface
 * @see     CollectorAwareInterface
 * @see     AddRouteInterface
 * @see     DebuggableInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Dispatcher extends EventCapableAbstract implements DispatcherInterface, HandlerAwareInterface, CollectorAwareInterface, ResolverAwareInterface, AddRouteInterface, DebuggableInterface
{
    use HandlerAwareTrait, CollectorAwareTrait, ResolverAwareTrait, AddRouteTrait, DebuggableTrait;

    /**#@+
     * Dispatcher level events
     *
     * @const
     */

    // just before finishing instantiation
    const EVENT_INIT = 'dispatcher.init';

    // before any matching starts
    const EVENT_BEFORE_MATCH = 'dispatcher.match.before';

    // after a successful matching
    const EVENT_AFTER_MATCH = 'dispatcher.match.after';

    // after a successful matching, before execute handler
    const EVENT_BEFORE_DISPATCH = 'dispatcher.dispatch.before';

    // after handler executed successfully
    const EVENT_AFTER_DISPATCH = 'dispatcher.dispatch.after';

    // before execute dispatcher's default handler
    const EVENT_BEFORE_HANDLER = 'dispatcher.handler.before';

    // after dispatcher's default handler executed
    const EVENT_AFTER_HANDLER = 'dispatcher.handler.after';
    /**#@-*/

    /**
     * The matching result
     *
     * @var    ResultInterface
     * @access protected
     */
    protected $result;

    /**
     * @param  CollectorInterface $collector
     * @param  ResolverInterface $resolver
     * @access public
     */
    public function __construct(
        CollectorInterface $collector = null,
        ResolverInterface $resolver = null
    ) {
        // inject first collector
        if ($collector) {
            $this->addCollector($collector);
        }

        // inject handler resolver
        if ($resolver) {
            $this->setResolver($resolver);
        }

        $this->trigger(self::EVENT_INIT);
    }

    /**
     * {@inheritDoc}
     */
    public function match(
        /*# string */ $httpMethod,
        /*# string */ $uriPath
    )/*# :  bool */ {
        $this->initResult($httpMethod, $uriPath);

        $param = ['result' => $this->result];
        if ($this->trigger(self::EVENT_BEFORE_MATCH, $param) &&
            $this->matchWithCollectors() &&
            $this->trigger(self::EVENT_AFTER_MATCH, $param)
        ) {
            return true;
        }
        return false;
    }

    /**
     * {@inheritDoc}
     */
    public function dispatch(
        /*# string */ $httpMethod,
        /*# string */ $uriPath
    )/*# : bool */ {
        if ($this->match($httpMethod, $uriPath)) {
            $param = ['result' => $this->result];
            if ($this->trigger(self::EVENT_BEFORE_DISPATCH, $param) &&
                $this->executeHandler() &&
                $this->trigger(self::EVENT_AFTER_DISPATCH, $param)
            ) {
                return true;
            }
        }
        return $this->dispatcherDefaultHandler();
    }

    /**
     * {@inheritDoc}
     */
    public function getResult()/*# : ResultInterface */
    {
        return $this->result;
    }

    /**
     * {@inheritDoc}
     */
    public function addRoute(RouteInterface $route)
    {
        // add a default collector if nothing found
        if (0 === count($this->getCollectors())) {
            $this->addCollector(new Collector());
        }

        // add route to the first collector
        $this->getCollectors()[0]->addRoute($route);

        return $this;
    }

    /**
     * Initialize the result
     *
     * @param  string $httpMethod
     * @param  string $uriPath
     * @access protected
     */
    protected function initResult($httpMethod, $uriPath)
    {
        $this->result = new Result($httpMethod, $uriPath);
    }

    /**
     * Match with all the route collectors of this dispatcher
     *
     * @return boolean
     * @access protected
     */
    protected function matchWithCollectors()/*# : bool */
    {
        foreach ($this->getCollectors() as $coll) {
            if ($coll->matchRoute($this->result)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Execute handler of the result
     *
     * @return bool
     * @access protected
     */
    protected function executeHandler()/*# : bool */
    {
        try {
            // resolve callable
            $handler = $this->result->getHandler();
            $callable = $this->getResolver()->resolve($handler);
            if (!is_callable($callable)) {
                return false;
            }
            return $this->runRouteCallable($callable);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Execute the callable
     *
     * @param  callable $callable
     * @return bool
     * @access protected
     */
    protected function runRouteCallable(callable $callable)/*# : bool */
    {
        $route = $this->result->getRoute();
        if (!$route) {
            return call_user_func($callable, $this->result);
        }

        /* @var EventCapableAbstract $route */
        if ($route->trigger(Route::EVENT_BEFORE_HANDLER) &&
            call_user_func($callable, $this->result) &&
            $route->trigger(Route::EVENT_AFTER_HANDLER)
        ) {
            return true;
        }
        return false;
    }

    /**
     * Execute dispatcher-level default handler
     *
     * @return bool
     * @access protected
     */
    protected function dispatcherDefaultHandler()/*# : bool */
    {
        $param = ['result' => $this->result];
        if ($this->trigger(self::EVENT_BEFORE_DEFAULT, $param)) {
            $handler = $this->getHandler($this->result->getStatus());
            if ($handler) {
                call_user_func($handler, $this->result);
            }
            $this->trigger(self::EVENT_AFTER_DEFAULT, $param);
        }
        return false;
    }
}
