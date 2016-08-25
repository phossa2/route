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

use Phossa2\Route\Status;
use Phossa2\Route\Message\Message;
use Phossa2\Shared\Debug\DebuggableInterface;
use Phossa2\Route\Interfaces\HandlerAwareInterface;

/**
 * HandlerAwareTrait
 *
 * Implementation of HandlerAwareInterface
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     HandlerAwareInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
trait HandlerAwareTrait
{
    /**
     * Different handler for different http status.
     *
     * @var    array
     * @access protected
     */
    protected $handlers = [];

    /**
     * {@inheritDoc}
     */
    public function addHandler($handler, /*# int */ $status = Status::OK)
    {
        $this->handlers[(int) $status] = $handler;

        // debug message
        if ($this instanceof DebuggableInterface) {
            $this->debug(Message::get(
                Message::RTE_HANDLER_ADD,
                is_object($handler) ? get_class($handler) : gettype($handler),
                get_class($this)
            ));
        }
        return $this;
    }

    /**
     * {@inheritDoc}
     */
    public function getHandler(/*# int */ $status)
    {
        if (isset($this->handlers[(int) $status])) {
            return $this->handlers[(int) $status];
        } elseif (isset($this->handlers[0])) {
            return $this->handlers[0];
        }
        return null;
    }
}
