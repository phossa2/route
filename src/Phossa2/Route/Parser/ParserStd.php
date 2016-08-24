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

/**
 * ParserStd
 *
 * Parser using common algorithm.
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @see     ParserAbstract
 * @version 2.0.0
 * @since   2.0.0 added
 */
class ParserStd extends ParserAbstract
{
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
     * chunk size 4 - 12 for merging regex
     *
     * @var    int
     * @access protected
     */
    protected $chunk = 8;

    /**
     * combined regex (cache)
     *
     * @var    string[]
     * @access protected
     */
    protected $data  = [];

    /**
     * {@inheritDoc}
     */
    public function processRoute(
        /*# string */ $routeName,
        /*# string */ $routePattern
    )/*# : string */ {
        $regex = $this->convert($routeName, $routePattern);
        $this->regex[$routeName] = $regex;
        $this->modified = true;

        // debug message
        $this->debug(Message::get(
            Message::RTE_PARSER_PATTERN,
            $routePattern,
            $regex
        ));

        return $regex;
    }

    /**
     * {@inheritDoc}
     */
    public function matchRoute(/*# string */ $uriPath)
    {
        $matches = [];
        foreach ($this->getRegexData() as $regex) {
            if (preg_match($regex, $uriPath, $matches)) {
                return $this->fixMatches($matches);
            }
        }
        return false;
    }

    /**
     * Convert to regex
     *
     * @param  string $name route name
     * @param  string $pattern route pattern
     * @return string parsed regex
     * @access protected
     */
    protected function convert(
        /*# string */ $name,
        /*# string */ $pattern
    )/*# : string */ {
        // regex
        $groupname = "\s*([a-zA-Z][a-zA-Z0-9_]*)\s*";
        $grouptype = ":\s*([^{}]*(?:\{(?-1)\}[^{}]*)*)";
        $placeholder = sprintf("\{%s(?:%s)?\}", $groupname, $grouptype);
        $segmenttype = "[^/]++";

        $result = preg_replace(
            [
                '~' . $placeholder . '(*SKIP)(*FAIL) | \[~x',
                '~' . $placeholder . '(*SKIP)(*FAIL) | \]~x',
                '~\{' . $groupname . '\}~x',
                '~' . $placeholder . '~x',
            ], [
                '(?:',  // replace '['
                ')?',   // replace ']'
                '{\\1:' . $segmenttype . '}',   // add segementtype
                '(?<${1}'. $name . '>${2})'   // replace groupname
            ], strtr('/' . trim($pattern, '/'), $this->shortcuts)
        );

        return empty($name) ? $result : ("(?<$name>" . $result . ")");
    }

    /**
     * Merge several (chunk size) regex into one
     *
     * @return array
     * @access protected
     */
    protected function getRegexData()/*# : array */
    {
        // load from cache
        if (empty($this->regex) || !$this->modified) {
            return $this->data;
        }

        // chunk size
        $this->data = array_chunk($this->regex, $this->chunk);

        // join in chunks
        foreach ($this->data as $i => $reg) {
            $this->data[$i] = '~^(?:' . implode('|', $reg) . ')$~x';
        }

        // save to cache here
        $this->modified = false;

        return $this->data;
    }

    /**
     * Fix matched placeholders, return with unique route name/key
     *
     * @param  array $matches
     * @return array [ $name, $matches ]
     * @access protected
     */
    protected function fixMatches($matches)/*# : array */
    {
        $this->removeNumericAndEmpty($matches);

        // get route key/name
        $routeName = array_keys($matches)[0];
        $len = strlen($routeName);

        // fix remainging match key
        $res = [];
        foreach ($matches as $key => $val) {
            if ($key != $routeName) {
                $res[substr($key, 0, -$len)] = $val;
            }
        }

        // debug
        $this->debug(Message::get(
            Message::RTE_PARSER_MATCH,
            $routeName,
            $this->regex[$routeName]
        ));

        return [$routeName, $res];
    }

    /**
     * remove numeric keys and empty group match
     *
     * @param  array $matches
     * @access protected
     */
    protected function removeNumericAndEmpty(array &$matches)
    {
        foreach ($matches as $idx => $val) {
            if (is_int($idx) || '' === $val) {
                unset($matches[$idx]);
            }
        }
    }
}