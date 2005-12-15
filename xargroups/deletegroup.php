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
function xproject_groups_deletegroup()
{
    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    list($gid,
         $gname,
         $confirmation) = xarVarCleanFromInput('gid',
                                              'gname',
                                              'confirmation');

    if (!xarSecAuthAction(0, 'Groups::', "$gname::$gid", ACCESS_DELETE)) {
        xarSessionSetVar('errormsg', _GROUPSDELNOAUTH);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }

    if (empty($confirmation)) {

        $output = new xarHTML();

        $func = xarVarCleanFromInput('func');
        if($func == "new") $output->Text(xarModAPIFunc('xproject','user','menu'));
        $output->ConfirmAction(_DELETEGROUPSURE,
                               xarModURL('xproject',
                                        'groups',
                                        'deletegroup'),
                               _CANCEL,
                               xarModURL('xproject',
                                        'groups',
                                        'main'),
                               array('gid' => $gid));

        return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject',
             'groups',
             'deletegroup', array('gid' => $gid))) {

        xarSessionSetVar('statusmsg', _GROUPDELETED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}

/*
 * adduser - user selection for a group
 */
?>