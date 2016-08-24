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

namespace Phossa2\Route\Message;

use Phossa2\Shared\Message\Message as BaseMessage;

/**
 * Message class for Phossa2\Route
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     \Phossa2\Shared\Message\Message
 * @version 2.0.0
 * @since   2.0.0 added
 */
class Message extends BaseMessage
{
    /**
     * Parse route pattern "%s" into "%s"
     */
    const RTE_PARSER_PATTERN = 1608231000;

    /**
     * Route matched with regex "%s" ("%s")
     */
    const RTE_PARSER_MATCH = 1608231001;

    /**
     * Route collector "%s" add parser "%s"
     */
    const RTE_PARSER_ADD = 1608231002;

    /**
     * Add route handler "%s" to "%s"
     */
    const RTE_HANDLER_ADD = 1608231010;

    /**
     * Unknown route handler "%s"
     */
    const RTE_HANDLER_UNKNOWN = 1608231011;

    /**
     * Route pattern "%s" malformed
     */
    const RTE_PATTERN_MALFORM = 1608231020;

    /**
     * Router add collector "%s"
     */
    const RTE_COLLECTOR_ADD = 1608231030;

    /**
     * Set handler "%s" from collector "%s"
     */
    const RTE_COLLECTOR_HANDLER = 1608231031;

    /**
     * Path "%s" matched with pattern "%s"
     */
    const RTE_COLLECTOR_MATCH = 1608231032;

    /**
     * Route "%s" duplicated for method "%s"
     */
    const RTE_ROUTE_DUPLICATED = 1608231040;

    /**
     * Route "%s" added for methods "%s"
     */
    const RTE_ROUTE_ADDED = 1608231041;

    /**
     * Route "%s" is disallowed for "%s"
     */
    const RTE_ROUTE_DISALLOWED = 1608231042;

    /**
     * Path "%s" matched with route "%s"
     */
    const RTE_ROUTE_MATCHED = 1608231043;

    /**
     * {@inheritDoc}
     */
    protected static $messages = [
        self::RTE_PARSER_PATTERN => 'Parse route pattern "%s" into "%s"',
        self::RTE_PARSER_MATCH => 'Route matched with regex "%s" ("%s")',
        self::RTE_PARSER_ADD => 'Route collector "%s" add parser "%s"',
        self::RTE_HANDLER_ADD => 'Add route handler "%s" to "%s"',
        self::RTE_HANDLER_UNKNOWN => 'Unknown route handler "%s"',
        self::RTE_PATTERN_MALFORM => 'Route pattern "%s" malformed',
        self::RTE_COLLECTOR_ADD => 'Router add collector "%s"',
        self::RTE_COLLECTOR_HANDLER => 'Set handler "%s" from collector "%s"',
        self::RTE_COLLECTOR_MATCH => 'Path "%s" matched with pattern "%s"',
        self::RTE_ROUTE_DUPLICATED => 'Route "%s" duplicated for method "%s"',
        self::RTE_ROUTE_ADDED => 'Route "%s" added for methods "%s"',
        self::RTE_ROUTE_DISALLOWED => 'Route "%s" is disallowed for "%s"',
        self::RTE_ROUTE_MATCHED => 'Path "%s" matched with route "%s"',
    ];
}
