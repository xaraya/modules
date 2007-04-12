<?php
/**
 *
 *
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
include_once("modules/xproject/xarincludes/teamsort.php");

function xproject_teamapi_getmembers($args)
{
    extract($args);

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
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
    
    $team_objectid = xarModGetVar('xproject','team_objectid');
    $fields = xarModAPIFunc('dynamicdata','user','getprop',
                            array('objectid' => $team_objectid));
    foreach ($fields as $name => $info) {
        if($name == "memberid") {
            $contact_type = $info['type'];
            switch($contact_type) {
                case 735:
                    include_once('modules/addressbook/xarglobal.php');
                    break;
                case 779:
                    include_once('modules/dossier/xarglobal.php');
                    break;
            }
            // 735 -> addressbook
            // 779 -> dossier
        }
    }
    
    if(!isset($contact_type)) $contact_type = 735;

    for (; !$result->EOF; $result->MoveNext()) {
        list($memberid) = $result->fields;
        
        if($memberid > 0) {
            switch($contact_type) {
                case 779:

                    $item = xarModAPIFunc('dossier', 'user', 'get', array('contactid' => $memberid));
                    $displayName = '';
                    $displayName .= "[".xarVarPrepHTMLDisplay($item['company'])."] ";
            
                    if ((!empty($item['fname']) && !empty($item['lname'])) ||
                        (!empty($item['fname']) || !empty($item['lname']))) {
                        if (xarModGetVar('dossier', 'name_order')==_DOSSIER_NO_FIRST_LAST) {
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
                    break;
                case 37:
                    $displayName = xarUserGetVar('uname', $memberid);
                    break;
                case 735:
                default:
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
                    break;
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


?>