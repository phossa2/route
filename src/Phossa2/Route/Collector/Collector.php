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

use Phossa2\Route\Status;
use Phossa2\Route\Message\Message;
use Phossa2\Route\Parser\ParserGcb;
use Phossa2\Route\Exception\LogicException;
use Phossa2\Route\Interfaces\RouteInterface;
use Phossa2\Route\Interfaces\ParserInterface;
use Phossa2\Route\Interfaces\ResultInterface;

/**
 * Collector
 *
 * Regular Expression Routing (RER)
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     CollectorAbstract
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Collector extends CollectorAbstract
{
    /**
     * pattern parser
     *
     * @var    ParserInterface
     * @access protected
     */
    protected $parser;

    /**
     * routes
     *
     * @var    array
     * @access protected
     */
    protected $routes = [];

    /**
     * Constructor
     *
     * @param  ParserInterface $parser
     * @param  array $properties
     * @access public
     */
    public function __construct(
        ParserInterface $parser = null,
        array $properties = []
    ) {
        $this->parser = $parser ?: new ParserGcb();
        parent::__construct($properties);

        if ($this->isDebugging()) {
            $this->debug(Message::get(
                Message::RTE_PARSER_ADD,
                get_class($this),
                get_class($this->parser)
            ));
            $this->delegateDebugger($this->parser);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function addRoute(RouteInterface $route)
    {
        // process pattern
        $routeKey = $this->getRouteKey($route);
        if (!isset($this->routes[$routeKey])) {
            $this->routes[$routeKey] = [];
            $this->parser->processRoute($routeKey, $route->getPattern());
        }

        $methods = $route->getMethods();
        foreach ($methods as $method) {
            $this->checkDuplication($route, $routeKey, $method);
            $this->routes[$routeKey][$method] = $route;
        }

        $this->debug(Message::get(
            Message::RTE_ROUTE_ADDED,
            $route->getPattern(),
            join('|', $methods)
        ));
        return $this;
    }

    /**
     * Same route pattern and method ?
     *
     * @param  RouteInterface $route
     * @param  string $routeKey
     * @param  string $method
     * @throws LogicException if duplication found
     * @access protected
     */
    protected function checkDuplication(
        RouteInterface $route,
        /*# string */ $routeKey,
        /*# string */ $method
    ) {
        if (isset($this->routes[$routeKey][$method])) {
            throw new LogicException(
                Message::get(
                    Message::RTE_ROUTE_DUPLICATED,
                    $route->getPattern(),
                    $method
                ),
                Message::RTE_ROUTE_DUPLICATED
            );
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function match(ResultInterface $result)/*# : bool */
    {
        $res = $this->parser->matchPath($result->getPath());
        if ($res) {
            list($routeKey, $params) = $res;
            return $this->getRoute($result, $routeKey, $params);
        }
        return false;
    }

    /**
     * Calculate route's unique key
     *
     * @param  RouteInterface $route
     * @return string
     * @access protected
     */
    protected function getRouteKey(RouteInterface $route)/*# : string */
    {
        return 'x' . substr(md5($route->getPattern()), -7);
    }

    /**
     * Get matched route
     *
     * @param  ResultInterface $result
     * @param  string $routeKey unique route key
     * @param  array $matches matched parameters
     * @return bool
     * @access protected
     */
    protected function getRoute(
        ResultInterface $result,
        /*# string */ $routeKey,
        array $matches
    )/*# : bool */ {
        $method = $result->getMethod();
        if (!isset($this->routes[$routeKey][$method])) {
            $result->setStatus(Status::METHOD_NOT_ALLOWED);
            return false;
        }
        $route = $this->routes[$routeKey][$method];
        $result->setStatus(Status::OK)->setRoute($route)
            ->setParameters(array_replace($route->getDefault(), $matches))
            ->setHandler($route->getHandler(Status::OK));

        $this->debug(Message::get(
            Message::RTE_ROUTE_MATCHED,
            $result->getPath(),
            $route->getPattern()
        ));
        return true;
    }
}
