<?php

function members_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminMembers',0)) {
        $menulinks[] = array('url'   => xarModURL('members',
                                                  'admin',
                                                  'view',
                                                  array('name' => 'members_members')),
                              'title' => xarML('View the members'),
                              'label' => xarML('View Members'));
        $menulinks[] = array('url'   => xarModURL('members',
                                                  'admin',
                                                  'modifyconfig'),
                              'title' => xarML('Modify the configuration settings'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>