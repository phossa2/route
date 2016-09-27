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

namespace Phossa2\Route\Resolver;

use Phossa2\Route\Message\Message;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Route\Exception\LogicException;
use Phossa2\Route\Interfaces\ResolverInterface;

/**
 * ResolverSimple
 *
 * Resolving ['controllerName', 'actionName'] into callable
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     ResolverInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class ResolverSimple extends ObjectAbstract implements ResolverInterface
{
    /**
     * @var    string
     * @access protected
     */
    protected $controller_suffix = 'Controller';

    /**
     * @var    string
     * @access protected
     */
    protected $action_suffix = 'Action';

    /**
     * Namespaces for controllers
     *
     * @var    string[]
     * @access protected
     */
    protected $namespaces = [];

    /**
     * @param  array $properties
     * @access public
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * {@inheritDoc}
     */
    public function resolve($handler)/*# : callable */
    {
        if (is_callable($handler)) {
            return $handler;
        } elseif (is_array($handler) && isset($handler[1])) {
            return $this->searchController($handler[0], $handler[1]);
        }
        throw new LogicException(
            Message::get(Message::RTE_HANDLER_UNKNOWN, $handler),
            Message::RTE_HANDLER_UNKNOWN
        );
    }

    /**
     * Search controller base on the name
     *
     * @param  string $controller
     * @param  string $action
     * @return callable
     * @throws LogicException if not found
     * @access protected
     */
    protected function searchController(
        /*# string */ $controller,
        /*# string */ $action
    )/*# : callable */ {
        foreach ($this->namespaces as $ns) {
            $class = $ns . '\\' . $controller . $this->controller_suffix;
            if (class_exists($class)) {
                $obj = new $class();
                return [$obj, $action . $this->action_suffix];
            }
        }
        throw new LogicException(
            Message::get(Message::RTE_HANDLER_UNKNOWN, $controller),
            Message::RTE_HANDLER_UNKNOWN
        );
    }
}
