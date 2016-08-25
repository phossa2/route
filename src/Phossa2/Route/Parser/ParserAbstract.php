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

namespace Phossa2\Route\Parser;

use Phossa2\Route\Message\Message;
use Phossa2\Shared\Base\ObjectAbstract;
use Phossa2\Shared\Debug\DebuggableTrait;
use Phossa2\Route\Interfaces\ParserInterface;
use Phossa2\Shared\Debug\DebuggableInterface;

/**
 * ParserAbstract
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ObjectAbstract
 * @see     ParserInterface
 * @see     DebuggableInterface
 * @version 2.0.0
 * @since   2.0.0 added
 */
abstract class ParserAbstract extends ObjectAbstract implements ParserInterface, DebuggableInterface
{
    use DebuggableTrait;

    /**
     * flag for new route added.
     *
     * @var    bool
     * @access protected
     */
    protected $modified = false;

    /**
     * regex storage
     *
     * @var    string[]
     * @access protected
     */
    protected $regex = [];

    /**
     * pattern shortcuts
     *
     * @var    string[]
     * @access protected
     */
    protected $shortcuts = [
        ':d}'   => ':[0-9]++}',             // digit only
        ':l}'   => ':[a-z]++}',             // lower case
        ':u}'   => ':[A-Z]++}',             // upper case
        ':a}'   => ':[0-9a-zA-Z]++}',       // alphanumeric
        ':c}'   => ':[0-9a-zA-Z+_\-\.]++}', // common chars
        ':nd}'  => ':[^0-9/]++}',           // not digits
        ':xd}'  => ':[^0-9/][^/]*+}',       // no leading digits
    ];

    /**
     * @var    string
     */
    const MATCH_GROUP_NAME = "\s*([a-zA-Z][a-zA-Z0-9_]*)\s*";
    const MATCH_GROUP_TYPE = ":\s*([^{}]*(?:\{(?-1)\}[^{}]*)*)";
    const MATCH_SEGMENT = "[^/]++";

    /**
     * Constructor
     *
     * @param  array $properties
     * @access public
     */
    public function __construct(array $properties = [])
    {
        $this->setProperties($properties);
    }

    /**
     * Update regex pool etc.
     *
     * @param  string $routeName
     * @param  string $routePattern
     * @param  string $regex
     * @access protected
     */
    protected function doneProcess(
        /*# string */ $routeName,
        /*# string */ $routePattern,
        /*# string */ $regex
    ) {
        $this->regex[$routeName] = $regex;
        $this->modified = true;

        // debug message
        $this->debug(Message::get(
            Message::RTE_PARSER_PATTERN,
            $routePattern,
            $regex
        ));
    }
}
