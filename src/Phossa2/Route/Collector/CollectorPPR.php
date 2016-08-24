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
use Phossa2\Route\Exception\LogicException;
use Phossa2\Route\Interfaces\RouteInterface;
use Phossa2\Route\Interfaces\ResultInterface;

/**
 * CollectorPPR
 *
 * Parameter Pairs Routing (PPR)
 *
 * Using parameter and value pairs like the following
 *
 * ```
 * http://servername/path/index.php/controller/action/id/1/name/nick
 * ```
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     CollectorAbstract
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CollectorPPR extends CollectorAbstract
{
    /**
     * {@inheritDoc}
     */
    public function addRoute(RouteInterface $route)
    {
        throw new LogicException(
            Message::get(Message::RTE_ROUTE_DISALLOWED, get_called_class()),
            Message::RTE_ROUTE_DISALLOWED
        );
    }

    /**
     * Parameter Pairs Routing (PPR)
     *
     * /path/index.php/controller/action/id/1/name/nick
     *
     * {@inheritDoc}
     */
    protected function match(ResultInterface $result)/*# : bool */
    {
        $parts = explode('/', trim($result->getPath(), '/'));
        if (count($parts) > 1) {
            return $this->processParts($parts, $result);
        }
        $result->setStatus(Status::BAD_REQUEST);
        return false;
    }

    /**
     *
     * @param  array $parts
     * @access protected
     */
    protected function processParts(
        array $parts,
        ResultInterface $result
    )/*# : bool */ {
        if (count($parts) % 2) {
            $result->setStatus(Status::BAD_REQUEST);
            return false;
        }

        $result->setStatus(Status::OK)
            ->setHandler($this->retrieveHandler($parts))
            ->setParameters($this->retrieveParams($parts));
        return true;
    }

    /**
     * Retrieve controller, action pair
     *
     * @param  array $data
     * @return array
     * @access protected
     */
    protected function retrieveHandler(array &$data)/*# : array */
    {
        $controller = array_shift($data);
        $action = array_shift($data);
        return [$controller, $action];
    }

    /**
     * Retrieve pair of params
     *
     * @param  array $data
     * @return array
     * @access protected
     */
    protected function retrieveParams(array $data)/*# : array */
    {
        $params = [];
        foreach ($data as $i => $val) {
            if (0 === $i % 2) {
                $params[$val] = $data[$i + 1];
            }
        }
        return $params;
    }
}
