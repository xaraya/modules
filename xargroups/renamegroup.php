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
function xproject_groups_renamegroup()
{
    list($gid,
     $gname,
     $confirmation) = xarVarCleanFromInput('gid',
                          'gname',
                          'confirmation');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }
    if (empty($confirmation)) {

    $output = new xarHTML();
    $func = xarVarCleanFromInput('func');
    if($func == "renamegroup") $output->Text(xarModAPIFunc('xproject','user','menu'));
    $output->ConfirmAction(_RENAMEGROUPSURE,
                           xarModURL('xproject', 'groups',
                                    'renamegroup'),
                           _CANCEL,
                           xarModURL('xproject', 'groups',
                                    'view'),
                           array('gid' => $gid,
                 'gname' => $gname));

    return $output->GetOutput();
    }
    if (xarModAPIFunc('xproject', 'groups',
             'renamegroup', array('gid'   => $gid,
                      'gname' => $gname))) {

    xarSessionSetVar('statusmsg', _GROUPRENAMED);
    }
    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));

    return true;
}
?>