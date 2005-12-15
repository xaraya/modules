<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_groups_adduser($args)
{
    extract($args);
    if (!xarVarFetch('gid',   'id', $gid,   0, XARVAR_NOT_REQUIRED)) return;

    // TODO: Add gid to Security check
    if (!xarSecurityCheck('AddXProject', 0, 'Group', "All:All:All")) {
        return;
    }

    $menu = xarModAPIFunc('xproject','user','menu');

    $group = xarModAPIFunc('xproject','groups','get',array('gid' => $gid));

    $output->Title(xarML('Add new team member') .' :: '. xarVarPrepForDisplay($group['gname']));

    $users = (xarModAPIFunc('xproject', 'groups', 'getmembers', array('eid' => $gid)));

    if($users == false) {
        $output->Text(_PMLOGMEMBERSFAILED);
        return $output->GetOutput();
    }

    $data['gid'] = $gid;
    $data['authid'] = xarSecGenAuthKey();

    $userlist = array();

    foreach($users as $user) {
    $userlist[] = array('id' => $user['uid'],
                'name' => $user['uname']);
    }
    $row = array();
    $output->SetOutputMode(_XH_RETURNOUTPUT);
    $row[] = $output->FormSelectMultiple('uid', $userlist);
    $row[] = $output->FormSubmit(xarML('Add member'));

    return $data;
}

?>