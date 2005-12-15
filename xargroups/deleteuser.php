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
function xproject_groups_deleteuser()
{
    list($gid,
         $uid,
         $confirmation) = xarVarCleanFromInput('gid',
                                              'uid',
                                              'confirmation');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_DELETE)) {
        xarSessionSetVar('errormsg', _GROUPSDELNOAUTH);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }
    if(empty($confirmation)) {

        $output = new xarHTML();
        $output->SetInputMode(_XH_VERBATIMINPUT);
        $func = xarVarCleanFromInput('func');
        if($func == "deleteuser") $output->Text(xarModAPIFunc('xproject','user','menu'));
        $output->ConfirmAction(xarML('Remove user from this group'),
                               xarModURL('xproject', 'groups',
                                        'deleteuser'),
                               _CANCEL,
                               xarModURL('xproject', 'groups',
                                        'view'),
                               array('gid' => $gid,
                                    'uid' => $uid));

        return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject', 'groups',
             'deleteuser', array('gid' => $gid,
                     'uid' => $uid))) {

        xarSessionSetVar('statusmsg', _USERDELETED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}

/*
 * modifygroup - modify group details
 */
?>