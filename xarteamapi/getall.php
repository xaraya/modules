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
function xproject_teamapi_getall($args)
{
    extract($args);

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'team', 'getall', 'xProject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if (!xarSecurityCheck('ViewXProject', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    if(!xarModAPILoad('addressbook', 'user')) return;

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $teamtable = $xartable['xProject_team'];

    $sql = "SELECT a.projectid,
                  b.project_name,
                  a.projectrole,
                  a.roleid,
                  a.memberid
            FROM $teamtable a, $projectstable b
            WHERE b.projectid = a.projectid
            AND a.projectid = $projectid";

    $result = $dbconn->Execute($sql);
    
    if (!$result) { // return;
        $msg = xarML('SQL: #(1)',
            $dbconn->ErrorMsg());
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    
    $items = array();

    for (; !$result->EOF; $result->MoveNext()) {
        list($projectid,
              $project_name,
              $projectrole,
              $roleid,
              $memberid) = $result->fields;
        
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
                                        
        $items[] = array('projectid'    => $projectid,
                          'project_name'=> $project_name,
                          'projectrole' => $projectrole,
                          'roleid'      => $roleid,
                          'memberid'    => $memberid,
                          'membername'  => $displayName);
    }

    $result->Close();

    return $items;
}

?>