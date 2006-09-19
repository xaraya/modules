<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_adminapi_chngimportance($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'project ID';
    }
    if (!isset($mode) || !is_string($mode)) {
        $invalid[] = 'mode';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
                            'user',
                            'get',
                            array('projectid' => $projectid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditXProject', 1, 'Item', "$item[project_name]:All:$projectid")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $project_table = $xartable['xProjects'];

    if($mode == "incr") {
        $query = "UPDATE $project_table
                SET importance = importance + 2
                WHERE projectid = ?";
        $importance = $item['importance'] + 2;
    } else {
        $query = "UPDATE $project_table
                SET importance = importance - 2
                WHERE projectid = ?";
        $importance = $item['importance'] - 2;
    }

    $bindvars = array($projectid);

    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;

    $userid = xarUserGetVar('uid');
    $logdetails = "Project modified.";
    $logdetails .= "<br>Project importance changed from ".$item['importance']." to ".$importance;

    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => $userid,
                            'details'        => $logdetails,
                            'changetype'    => "MODIFIED"));

    return true;
}
?>