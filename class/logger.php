<?php
/**
 * Workflow Module PSR-3 LoggerInterface compliant bridge to xarLog::message() for Symfony & other packages
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

class xarWorkflowLogger extends AbstractLogger
{
    private $mapping = [
        LogLevel::EMERGENCY => xarLog::LEVEL_EMERGENCY,  // 'emergency'
        LogLevel::ALERT     => xarLog::LEVEL_ALERT,      // 'alert'
        LogLevel::CRITICAL  => xarLog::LEVEL_CRITICAL,   // 'critical'
        LogLevel::ERROR     => xarLog::LEVEL_ERROR,      // 'error'
        LogLevel::WARNING   => xarLog::LEVEL_WARNING,    // 'warning'
        LogLevel::NOTICE    => xarLog::LEVEL_NOTICE,     // 'notice'
        LogLevel::INFO      => xarLog::LEVEL_INFO,       // 'info'
        LogLevel::DEBUG     => xarLog::LEVEL_DEBUG,      // 'debug'
    ];

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string|\Stringable $message
     * @param array $context
     *
     * @return void
     *
     * @throws \Psr\Log\InvalidArgumentException
     */
    public function log($level, string|\Stringable $message, array $context = []): void
    {
        xarLog::message($this->interpolate($message, $context), $mapping[$level] ?? xarLog::LEVEL_INFO);
    }

    /**
     * Interpolates context values into the message placeholders.
     * Source: https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-3-logger-interface.md
     */
    public function interpolate($message, array $context = array())
    {
        // build a replacement array with braces around the context keys
        $replace = array();
        foreach ($context as $key => $val) {
            // check that the value can be cast to string
            if (!is_array($val) && (!is_object($val) || method_exists($val, '__toString'))) {
                $replace['{' . $key . '}'] = $val;
            }
        }

        // interpolate replacement values into the message and return
        return strtr($message, $replace);
    }
}
