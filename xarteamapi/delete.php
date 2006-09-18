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
function xproject_teamapi_delete($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }

    if (!isset($memberid) || !is_numeric($memberid)) {
        $invalid[] = 'memberid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'feature ID', 'team', 'delete', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    // does it exist ?
    $item = xarModAPIFunc('xproject',
                            'team',
                            'get',
                            array('projectid' => $projectid,
                                'memberid' => $memberid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    if (!xarSecurityCheck('DeleteXProject', 1, 'Item', "$item[project_name]:All:$item[projectid]")) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $teamtable = $xartable['xProject_team'];

    $sql = "DELETE FROM $teamtable
            WHERE projectid = $projectid
            AND memberid = $memberid";
    $result = $dbconn->Execute($sql);

    if (!$result) return;

    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;

    $displayName = '';
    if(xarModIsAvailable('addressbook') && xarModAPILoad('addressbook', 'user')) {
        $item = xarModAPIFunc('addressbook', 'user', 'getDetailValues', array('id' => $memberid));
        $displayName .= xarVarPrepHTMLDisplay($item['company'])."<br>";

        if ((!empty($item['fname']) && !empty($item['lname'])) ||
            (!empty($item['fname']) || !empty($item['lname']))) {
            if (xarModGetVar('addressbook', 'name_order')==_AB_NO_FIRST_LAST) {
                if (!empty($prefixes) && $item['prefix'] > 0) {
                    $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                }
                $displayName .= xarVarPrepHTMLDisplay($item['fname']).' '.xarVarPrepHTMLDisplay($item['lname']);
            } else {
                if (!empty($item['lname'])) {
                    $displayName .= xarVarPrepHTMLDisplay($item['lname']).', ';
                }
                if (!empty($prefixes) && $item['prefix'] > 0) {
                    $displayName .= $prefixes[$item['prefix']-1]['name'].' ';
                }
                $displayName .= xarVarPrepHTMLDisplay($item['fname']);
            }
        }
    }
    $userdetails = $displayName;

    $logdetails = "Team member removed: ".$userdetails.".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "TEAM"));

    // Let the calling process know that we have finished successfully
    return true;
}

?>
