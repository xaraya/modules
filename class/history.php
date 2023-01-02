<?php
/**
 * Workflow Module History for Symfony Workflow events
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

sys::import('modules.dynamicdata.class.objects.master');

class xarWorkflowHistory extends xarObject
{
    private static $objectName = 'workflow_history';
    private static $objectList;
    private static $objectRef;

    public static function init(array $args = [])
    {
    }
}
