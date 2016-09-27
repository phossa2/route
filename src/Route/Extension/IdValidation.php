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

namespace Phossa2\Route\Extension;

use Phossa2\Route\Route;
use Phossa2\Route\Status;
use Phossa2\Event\Interfaces\EventInterface;
use Phossa2\Route\Interfaces\ResultInterface;
use Phossa2\Event\EventableExtensionAbstract;

/**
 * IdValidation
 *
 * Validate a `id` value. Can be used on a route.
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionAbstract
 * @see     ResultInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class IdValidation extends EventableExtensionAbstract
{
    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['validate'];
    }

    /**
     * Extension methods takes an event as input.
     *
     * Event params has `Phossa2\Route\Result` set as 'result'
     *
     * MUST RETURN A BOOLEAN VALUE !!!
     *
     * @param  EventInterface $event
     * @return bool
     * @access protected
     */
    public function validate(EventInterface $event)/*# : bool */
    {
        /* @var ResultInterface $result */
        $result = $event->getParam('result');
        $params = $result->getParameters();

        if ($params['id'] < 2000) {
            $result->setStatus(Status::PRECONDITION_FAILED);
            return false;
        }
        return true;
    }

    /**
     * Return event handlers of this extension handling
     *
     * ```php
     * protected function extensionHandles()
     * {
     *     return [
     *         ['event' => 'cache.*', 'handler' => ['byPassCache', 100]],
     *     ];
     * }
     * ```
     *
     * @return array
     * @access protected
     */
    protected function extensionHandles()/*# : array */
    {
        return [
            [
                'event' => Route::EVENT_BEFORE_HANDLER,
                'handler' => 'validate'
            ]
        ];
    }
}
