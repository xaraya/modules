<?php

function xproject_groups_insertuser()
{
    list($gid,
     $uid) = xarVarCleanFromInput('gid',
                    'uid');

    if (!xarSecConfirmAuthKey()) {
        xarSessionSetVar('errormsg', _BADAUTHKEY);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
        return true;
    }

    if (!xarSecAuthAction(0, 'Groups::', "::", ACCESS_ADD)) {
        xarSessionSetVar('errormsg', _GROUPSDELNOAUTH);
        xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
    }

    if (xarModAPIFunc('xproject',
                    'groups',
                    'insertuser', array('gid' => $gid,
                                        'uid' => $uid))) {

        xarSessionSetVar('statusmsg', _USERADDED);
    }

    xarResponseRedirect(xarModURL('xproject', 'groups', 'main'));
}

?>