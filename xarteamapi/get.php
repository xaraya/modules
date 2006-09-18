<?php

function xproject_teamapi_get($args)
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
                    'feature ID', 'user', 'get', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $projectstable = $xartable['xProjects'];
    $teamtable = $xartable['xProject_team'];

    $query = "SELECT a.projectid,
                    b.project_name,
                    a.projectrole,
                    a.roleid,
                    a.memberid
            FROM $teamtable a, $projectstable b
            WHERE b.projectid = a.projectid
            AND a.projectid = ?
            AND a.memberid = ?";
    $result = &$dbconn->Execute($query,array($projectid, $memberid));

    if (!$result) return;

    if ($result->EOF) {
        $result->Close();
//        $msg = xarML('This item does not exist');
//        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
//                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        $item = array();
        return $item;
    }

    list($project_id,
          $project_name,
          $projectrole,
          $role_id,
          $member_id) = $result->fields;

    $result->Close();

    if($member_id > 0) {
        $item = xarModAPIFunc('addressbook', 'user', 'getDetailValues', array('id' => $member_id));
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
        $displayName = xarUserGetVar('uname', $role_id);
    }

    if($project_id > 0 && $member_id > 0) {
        $item = array('projectid' => $project_id,
                    'project_name' => $project_name,
                    'projectrole' => $projectrole,
                    'roleid' => $role_id,
                    'memberid' => $member_id,
                    'membername'  => $displayName);
        return $item;
    }

    return;
}

?>