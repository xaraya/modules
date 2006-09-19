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
/**
 * Administration System
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_teamapi_getmembers($args)
{
    extract($args);

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $teamtable = $xartable['xProject_team'];

    $sql = "SELECT DISTINCT
                  memberid
            FROM $teamtable";

    $result = $dbconn->Execute($sql);

    if (!$result) return;

    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($memberid) = $result->fields;

        if($memberid > 0) {
            $item = xarModAPIFunc('addressbook', 'user', 'getDetailValues', array('id' => $memberid));
            $displayName = '';
            $displayName .= "[".xarVarPrepHTMLDisplay($item['company'])."] ";

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
        } elseif($roleid > 0) {
            $displayName = xarUserGetVar('uname', $roleid);
        }

        $items[] = array('memberid'    => $memberid,
                         'membername'  => $displayName);
    }

    usort($items, "teamsort");

    $result->Close();

    return $items;
}

function teamsort($a, $b)
{
    if ($a['membername'] == $b['membername']) {
        return 0;
    }
    return ($a['membername'] < $b['membername']) ? -1 : 1;
}
?>