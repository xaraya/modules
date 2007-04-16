<?php
/**
 * Workflow Module
 *
 * @package modules
 * @copyright (C) 2003-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Workflow Module
 * @link http://xaraya.com/index.php/release/188.html
 * @author Workflow Module Development Team
 */
sys::import('modules.workflow.lib.galaxia.api');
/**
 * the processes administration function
 *
 * @author mikespub
 * @access public
 */
function workflow_admin_processes()
{
    xarLogMessage('WF: workflow_admin_processes ');
    // Security Check
    if (!xarSecurityCheck('AdminWorkflow')) return;

    // Common setup for Galaxia environment
    include_once('modules/workflow/tiki-setup.php');
    $data = array();

    // Adapted from tiki-g-admin_processes.php
    include_once(GALAXIA_LIBRARY.'/processmanager.php');

    // Initialize
    $data['proc_info'] = array(
        'name'          => '',
        'description'   => '',
        'version'       => '1.0',
        'isActive'      => 'n',
        'pId'           => 0);

    // Check if we are editing an existing process
    // if so retrieve the process info and assign it.
    if (!isset($_REQUEST['pid'])) $_REQUEST['pid'] = 0;
    if ($_REQUEST['pid']) {
        $process = new Process($_REQUEST['pid']);
        $data['proc_info'] = $processManager->get_process($_REQUEST["pid"]);
        $data['proc_info']['graph'] = $process->getGraph();
    }
    $data['pid'] =  $_REQUEST['pid'];

    //Check here for an uploaded process
    xarLogMessage('WF: checking for uploaded process');
    if (isset($_FILES['userfile1']) && is_uploaded_file($_FILES['userfile1']['tmp_name'])) {
        xarLogMessage('WF: Found upload file');
        // move the uploaded file to some temporary wf* file in cache/templates
        $tmpdir = xarCoreGetVarDirPath() . '/cache/templates';
        $tmpfile = tempnam($tmpdir, 'wf');
        if (move_uploaded_file($_FILES['userfile1']['tmp_name'], $tmpfile) && file_exists($tmpfile)) {
            xarLogMessage('WF: Temporary upload file found, reading it in.');
            $fp = fopen($tmpfile, "rb");

            $xml = ''; $fhash = '';
            // Read it in
            while (!feof($fp)) $xml .= fread($fp, 8192 * 16);

            fclose ($fp);
            $size = $_FILES['userfile1']['size'];
            $name = $_FILES['userfile1']['name'];
            $type = $_FILES['userfile1']['type'];

            $process_data = $processManager->unserialize_process($xml);

            if (Process::exists($process_data['name'], $process_data['version'])) {
                $data['msg'] =  xarML("The process name already exists");
                return xarTplModule('workflow', 'admin', 'error', $data);
            } else {
                $processManager->import_process($process_data);
            }
            unlink($tmpfile);
        }
    }
    xarLogMessage('WF: done with the uploading');

    if (isset($_REQUEST["delete"])) {
        foreach (array_keys($_REQUEST["process"])as $item) {
            $processManager->remove_process($item);
        }
    }

    // New minor version of the process
    if (isset($_REQUEST['newminor'])) {
        $processManager->new_process_version($_REQUEST['newminor']);
    }

    // New major version of the process
    if (isset($_REQUEST['newmajor'])) {
        $processManager->new_process_version($_REQUEST['newmajor'], false);
    }

    // Update or create action
    if (isset($_REQUEST['save'])) {
        $vars = array('name' => $_REQUEST['name'],
                      'description' => $_REQUEST['description'],
                      'version' => $_REQUEST['version'],
                      'isActive' => 'n'
                      );

        // If process is known and we're not updating, error out.
        if (Process::Exists($_REQUEST['name'], $_REQUEST['version']) && $_REQUEST['pid'] == 0) {
            $data['msg'] =  xarML("Process already exists");
            return xarTplModule('workflow', 'admin', 'error', $data);
        }

        if (isset($_REQUEST['isActive']) && $_REQUEST['isActive'] == 'on') {
            $vars['isActive'] = 'y';
        }
        // Replace the info on the process with the new values (or create them)
        $pid = $processManager->replace_process($_REQUEST['pid'], $vars);
        $process = new Process($pid);
        // Validate the process and deactivate it if it turns out to be invalid.
        $valid = $activityManager->validate_process_activities($pid);
        if (!$valid) $process->deactivate();

        // Reget the process info for the UI
        $process = new Process($pid);
        $data['proc_info'] = $processManager->get_process($pid);
        $data['proc_info']['graph'] = $process->getGraph();
    }

    // Filtering by name, status or direct
    $data['where'] = '';
    $wheres = array();
    if (isset($_REQUEST['filter'])) {
        if ($_REQUEST['filter_name'])   $wheres[]=" name='".$_REQUEST['filter_name']."'";
        if ($_REQUEST['filter_active']) $wheres[]=" isActive='" . $_REQUEST['filter_active']."'";
        $data['where'] = implode('and', $wheres);
    }
    if (isset($_REQUEST['where'])) $data['where'] = $_REQUEST['where'];

    // Specific sorting specified?
    $data['sort_mode'] = isset($_REQUEST["sort_mode"]) ? $_REQUEST["sort_mode"] : 'lastModif_desc';
    // Offset into the processlist
    $data['offset'] = isset($_REQUEST["offset"]) ? $_REQUEST["offset"] : 1;
    // Specific find text
    $data['find'] = isset($_REQUEST["find"]) ? $_REQUEST["find"] : '';

    // MaxRecords comes from tiki-setup.php (modvar)
    $items = $processManager->list_processes($data['offset'] - 1, $maxRecords, $data['sort_mode'], $data['find'], $data['where']);
    $data['cant'] =  $items['cant'];

    $data['cant_pages'] =  ceil($items["cant"] / $maxRecords);
    $data['actual_page'] =  1 + (($data['offset'] - 1) / $maxRecords);

    $data['next_offset'] =  -1;
    if ($items["cant"] >= ($data['offset'] + $maxRecords)) {
        $data['next_offset'] =  $data['offset'] + $maxRecords;
    }

    $data['prev_offset'] =  -1;
    if ($data['offset'] > 1) {
        $data['prev_offset'] =  $data['offset'] - $maxRecords;
    }
    $data['items'] =  $items["data"];

    // Validate the process
    if ($_REQUEST['pid']) {
        $valid = $activityManager->validate_process_activities($_REQUEST['pid']);
        $data['errors'] = array();
        if (!$valid) {
            $process = new Process($_REQUEST['pid']);
            $process->deactivate();
            $data['errors'] = $activityManager->get_error();
        }
    }

    // Huh? why?
    $items = $processManager->list_processes(0, -1, 'name_desc', '', '');
    $data['all_procs'] =  $items['data'];

    $url = xarServerGetCurrentURL(array('offset' => '%%'));
    $data['pager'] = xarTplGetPager($data['offset'], $items['cant'], $url, $maxRecords);
    return $data;
}

?>
