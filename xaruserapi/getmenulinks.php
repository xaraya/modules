<?php

function members_userapi_getmenulinks()
{
    if (xarUserIsLoggedIn()){
        if (xarSecurityCheck('AdminMembers',0)) {
                $menulinks[] = array('url'   => xarModURL('members',
                                                          'user',
                                                          'view'),
                                     'title' => xarML('View All Users'),
                                     'label' => xarML('Member list'));
        }
        if (empty($menulinks)){
            $menulinks = '';
        }
    }
    return $menulinks;
}

?>
