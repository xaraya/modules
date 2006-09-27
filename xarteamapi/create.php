<?php
/**
 * Administration System
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 *
 * @subpackage xproject module
 * @author Chad Kraeft <stego@xaraya.com>
*/
function xproject_teamapi_create($args)
{
    extract($args);

    if(!xarModLoad('addressbook', 'user')) return;

    $invalid = array();
    if (!isset($projectid) || !is_numeric($projectid)) {
        $invalid[] = 'projectid';
    }
    if (!isset($memberid) || !is_numeric($memberid)) {
        $invalid[] = 'memberid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!xarSecurityCheck('AddXProject', 1, 'Item', "All:All:All")) {
        $msg = xarML('Not authorized to add #(1) items',
                    'xproject');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }

    $item = xarModAPIFunc('xproject',
                            'team',
                            'get',
                            array('projectid' => $projectid,
                                'memberid' => $memberid));

    if (isset($item['projectid']) && isset($item['memberid'])) {
        xarSessionSetVar('statusmsg', xarML('Team Member already assigned'));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $teamtable = $xartable['xProject_team'];

    $nextId = $dbconn->GenId($teamtable);

    $query = "INSERT INTO $teamtable (
                  projectid,
                  projectrole,
                  memberid)
            VALUES (?,?,?)";

    $bindvars = array(
              $projectid,
              $projectrole,
              $memberid);

    $result = &$dbconn->Execute($query,$bindvars);
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

    $logdetails = "Team member added: ".$userdetails.".";
    $logid = xarModAPIFunc('xproject',
                        'log',
                        'create',
                        array('projectid'   => $projectid,
                            'userid'        => xarUserGetVar('uid'),
                            'details'        => $logdetails,
                            'changetype'    => "TEAM"));

    return true;
}

?>