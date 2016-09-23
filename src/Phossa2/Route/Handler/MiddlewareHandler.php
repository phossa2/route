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

namespace Phossa2\Route\Handler;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Route\Interfaces\ResultInterface;
use Phossa2\Route\Interfaces\HandlerInterface;

/**
 * MiddlewareHandler
 *
 * Handler result with middlewares
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     HandlerInterface
 * @see     RequestInterface
 * @see     ResponseInterface
 * @version 2.0.1
 * @since   2.0.1 added
 */
class MiddlewareHandler extends ObjectAbstract implements HandlerInterface
{
    /**
     * @var    mixed
     * @access protected
     */
    protected $middleware;

    /**
     * @param  mixed $middleware
     * @access public
     */
    public function __construct($middleware)
    {
        $this->middleware = $middleware;
    }

    /**
     * Handles the result
     *
     * @param  ResultInterface $result
     * @access public
     * @api
     */
    public function __invoke(ResultInterface $result)
    {
        $params = $result->getParameters();
        $middleware = $this->middleware;
        $middleware($params['request'], $params['response']);
    }
}

