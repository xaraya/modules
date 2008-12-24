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
**/
sys::import('modules.workflow.lib.galaxia.api');
/**
 * the shared source administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_shared_source()
{
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

    // Common setup for Galaxia environment
    sys::import('modules.workflow.lib.galaxia.config');
    $tplData = array();

    // Adapted from tiki-g-admin_shared_source.php

    include_once(GALAXIA_LIBRARY.'/processmanager.php');

    if (!isset($_REQUEST['pid'])) {
        $tplData['msg'] =  xarML("No process indicated");
        return xarTplModule('workflow', 'admin', 'error', $tplData);
    }

    $tplData['pid'] =  $_REQUEST['pid'];

    if (isset($_REQUEST['code'])) {
        unset ($_REQUEST['template']);
        $_REQUEST['save'] = 'y';
    }

    $process = new Process($_REQUEST['pid']);
    $proc_info = $processManager->get_process($_REQUEST['pid']);
    $proc_info['graph']=$process->getGraph();
    $tplData['proc_info'] =& $proc_info;

    $procname = $process->getNormalizedName();

    $tplData['warn'] =  '';

    if (!isset($_REQUEST['activityId']))
        $_REQUEST['activityId'] = 0;

    $tplData['activityId'] =  $_REQUEST['activityId'];

    if ($_REQUEST['activityId']) {
        $act = WorkFlowActivity::get($_REQUEST['activityId']);

        $actname = $act->getNormalizedName();

        if (isset($_REQUEST['template'])) {
            $tplData['template'] =  'y';

            $source = GALAXIA_PROCESSES."/$procname/code/templates/$actname" . '.tpl';
        } else {
            $tplData['template'] =  'n';

            $source = GALAXIA_PROCESSES."/$procname/code/activities/$actname" . '.php';
        }

        // Then editing an activity
        $tplData['act_info'] =  array(
            'isInteractive' => $act->isInteractive() ? 'y' : 0,
            'type'          => $act->getType());
    } else {
        $tplData['template'] =  'n';
        $tplData['act_info'] =  array('isInteractive' => 'n', 'type' => 'shared');
        // Then editing shared code
        $source = GALAXIA_PROCESSES."/$procname/code/shared.php";
    }

    //First of all save
    if (isset($_REQUEST['source'])) {
        // security check on paths
        $basedir = GALAXIA_PROCESSES . "/$procname/code/";
        $basepath = realpath($basedir);
        $sourcepath = realpath($_REQUEST['source_name']);
        if (substr($sourcepath,0,strlen($basepath)) == $basepath) {
            $fp = fopen($_REQUEST['source_name'], "wb");

            if (get_magic_quotes_gpc()) {
                $_REQUEST['source'] = stripslashes($_REQUEST['source']);
            }
            fwrite($fp, $_REQUEST['source']);
            fclose ($fp);
            if ($_REQUEST['activityId']) {
                $act = WorkflowActivity::get($_REQUEST['activityId']);
                $act->compile();
            }
        } else {
            die('potential hack attack');
        }
    }

    $tplData['source_name'] =  $source;

    $fp = fopen($source, "rb");
    $tplData['data'] = '';
    while (!feof($fp)) {
        $data = fread($fp, 4096);
        $tplData['data'] .=  htmlspecialchars($data);
    }
    fclose ($fp);

    $valid = $activityManager->validate_process_activities($_REQUEST['pid']);
    $errors = array();

    if (!$valid) {
        $errors = $activityManager->get_error();

        $proc_info['isValid'] = 'n';
    } else {
        $proc_info['isValid'] = 'y';
    }

    $tplData['errors'] =  $errors;

    $activities = $activityManager->list_activities($_REQUEST['pid'], 0, -1, 'name_asc', '');
    $tplData['items'] = $activities['data'];

    $tplData['mid'] =  'tiki-g-admin_shared_source.tpl';

    return $tplData;
}
?>
