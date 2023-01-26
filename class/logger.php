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

sys::import('xaraya.bridge.logging');
use Xaraya\Bridge\Logging\LoggerBridge;

class xarWorkflowLogger extends LoggerBridge
{
}
