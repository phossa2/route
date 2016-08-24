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

use Phossa2\Route\Message\Message;

/*
 * Provide zh_CN translation
 *
 * @package Phossa2\Route
 * @author  Hong Zhang <phossa@126.com>
 * @version 2.0.0
 * @since   2.0.0 added
 */
return [
    Message::RTE_PARSER_PATTERN => '处理路由模式 "%s" 成 "%s"',
    Message::RTE_PARSER_MATCH => '路由匹配成功 "%s" ("%s")',
    Message::RTE_PARSER_ADD => '路由集合 "%s" 添加路由解析器 "%s"',
    Message::RTE_HANDLER_ADD => '添加路由处理器 "%s" 到 "%s"',
    Message::RTE_HANDLER_UNKNOWN => '未知路由处理器 "%s"',
    Message::RTE_PATTERN_MALFORM => '路由匹配模式 "%s" 形式错误',
    Message::RTE_COLLECTOR_ADD => '添加路由集合 "%s"',
    Message::RTE_COLLECTOR_HANDLER => '设置处理器 "%s" (来自路由集合 "%s")',
    Message::RTE_COLLECTOR_MATCH => '路径 "%s" 匹配了模式 "%s"',
    Message::RTE_ROUTE_DUPLICATED => '路由定义 "%s"重复了(方法 "%s")',
    Message::RTE_ROUTE_ADDED => '添加了路由定义 "%s" (适用于 "%s")',
    Message::RTE_ROUTE_DISALLOWED => '路由定义 "%s" 在  "%s" 中被禁止',
    Message::RTE_ROUTE_MATCHED => '路径 "%s" 与路由 "%s" 匹配成功',
];
