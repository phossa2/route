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

use Phossa2\Event\Interfaces\EventInterface;
use Phossa2\Route\Interfaces\ResultInterface;
use Phossa2\Event\EventableExtensionAbstract;
use Phossa2\Route\Collector\CollectorAbstract;

/**
 * CollectorStats
 *
 * Collecting statistics of a collector
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     EventableExtensionAbstract
 * @see     ResultInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
class CollectorStats extends EventableExtensionAbstract
{
    /**
     * Total routes trying to match
     * @var    int
     * @access protected
     */
    protected $total = 0;

    /**
     * Number of routes matched
     *
     * @var    int
     * @access protected
     */
    protected $matched = 0;

    /**
     * {@inheritDoc}
     */
    public function methodsAvailable()/*# : array */
    {
        return ['beforeMatch', 'afterMatch', 'getStats'];
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
    public function beforeMatch(EventInterface $event)/*# : bool */
    {
        ++$this->total;
        return true;
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
    public function afterMatch(EventInterface $event)/*# : bool */
    {
        ++$this->matched;
        return true;
    }

    /**
     * Returns the stats collected
     * @access public
     */
    public function getStats()
    {
        echo sprintf(
            "Total %d Matched %d (%s%%)",
            $this->total,
            $this->matched,
            number_format($this->matched * 100.0 / $this->total, 1)
        );
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
                'event' => CollectorAbstract::EVENT_BEFORE_MATCH,
                'handler' => 'beforeMatch'
            ],
            [
                'event' => CollectorAbstract::EVENT_AFTER_MATCH,
                'handler' => 'afterMatch'
            ],
        ];
    }
}
