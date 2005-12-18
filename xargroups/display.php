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
function xproject_groups_display($args)
{
    extract($args);

    if (!isset($gid)) {
        xarSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $output = new xarHTML();

    if(empty($groupid)) $groupid = xarVarCleanFromInput('gid');
    xarSessionSetVar('groupid',$groupid);


    $output->SetInputMode(_XH_VERBATIMINPUT);
    $func = xarVarCleanFromInput('func');
    if($func == "viewgroup") $output->Text(xarModAPIFunc('xproject','user','menu'));
    $output->LineBreak();

    $group = xarModAPIFunc('xproject','groups','get',array('gid' => $gid));

    $output->Title(xarML('Team members') .': '.  xarVarPrepForDisplay($group['gname']));
    $output->URL(xarModURL('xproject',
                          'groups',
                          'adduser', array('gid' => $group['gid'])),
                xarML('Add team member'));
    $output->LineBreak();
    $output->SetInputMode(_XH_PARSEINPUT);

    $tableHead = array(xarML('Member name'), _OPTION);

    $output->TableStart('', $tableHead, 1);

    $users = xarModAPIFunc('xproject',
              'groups',
              'getmembers', array('gid' => $group['gid']));

    if ($users == false) {
        $output->Text('No users in this group');
        $output->LineBreak();
        $output->TableEnd();
        return $output->GetOutput();
    }

    foreach($users as $user) {

        $output->SetOutputMode(_XH_RETURNOUTPUT);
        if (xarSecAuthAction(0, 'Groups::', "::", ACCESS_DELETE)) {
            $action = $output->URL(xarModURL('xproject',
                                               'groups',
                                               'deleteuser', array('gid'    => $group['gid'],
                                                                   'uid'    => $user['uid'],
                                                                   'authid' => xarSecGenAuthKey())), _DELETE);
        }
        $output->SetOutputMode(_XH_KEEPOUTPUT);

        $row = array(xarVarPrepForDisplay($user['uname']), $action);

        $output->SetInputMode(_XH_VERBATIMINPUT);
        $output->TableAddRow($row);
        $output->SetInputMode(_XH_PARSEINPUT);
    }
    $output->TableEnd();

    return $output->GetOutput();
}
?>