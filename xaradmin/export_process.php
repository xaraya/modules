<?php
/**
 * Workflow Module
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
/**
 * the save process administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_export_process()
{
    // Security Check
    if (!xarSecurity::check('AdminWorkflow')) {
        return;
    }

    if (!xarVar::fetch('pid', 'int', $data['processid'], 0, xarVar::NOT_REQUIRED)) {
        return;
    }

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $tplData = [];

    // Adapted from tiki-g-save_process.php

    include_once(GALAXIA_LIBRARY.'/processmanager.php');

    // The galaxia process manager PHP script.

    // Check if we are editing an existing process
    // if so retrieve the process info and assign it.
    if (!isset($_REQUEST['pid'])) {
        $_REQUEST['pid'] = 0;
    }

    $data['xml'] = htmlentities($processManager->serialize_process($_REQUEST['pid']));
    $data['fields'] = $processManager->get_process($_REQUEST['pid']);
    return $data;
}
