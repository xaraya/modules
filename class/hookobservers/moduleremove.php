<?php
/**
 * Workflow Module ModuleRemove API Hook Observer
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

sys::import('modules.workflow.class.hookobserver');

class WorkflowModuleRemoveObserver extends xarWorkflowHookObserver implements ixarEventObserver
{
    public $module = 'workflow';
    public $type   = 'admin';
    public $func   = 'removehook';
}
