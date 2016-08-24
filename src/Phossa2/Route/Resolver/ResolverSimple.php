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
     * {@inheritDoc}
     */
    public function resolve($handler)/*# : callable */
    {
        // callable
        if (is_callable($handler)) {
            return $handler;

        // append Controller/Action
        } elseif (is_array($handler)) {
            $controller = $handler[0] . 'Controller';
            $action = $handler[1] . 'Action';
            $result = [$controller, $action];
            if (is_callable($result)) {
                return $result;
            }
        }

        // unknown
        throw new LogicException(
            Message::get(Message::RTE_HANDLER_UNKNOWN, $handler),
            Message::RTE_HANDLER_UNKNOWN
        );
    }
}
