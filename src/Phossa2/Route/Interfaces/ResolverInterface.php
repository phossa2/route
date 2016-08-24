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

namespace Phossa2\Route\Interfaces;

use Phossa2\Route\Exception\LogicException;

/**
 * ResolverInterface
 *
 * Resolving the result of `HandlerAwareInterface::getHandler()` into callable
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
interface ResolverInterface
{
    /**
     * Resolve the given handler
     *
     * @param  mixed $handler the given handler
     * @return callable
     * @throws LogicException if resolving failed
     * @access public
     */
    public function resolve($handler)/*# : callable */;
}
