<?php

function vendors_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminVendors',0)) {
        $menulinks[] = Array('url'   => xarModURL('vendors', 'admin', 'suppliers'),
                              'title' => xarML('Add, edit, delete vendors'),
                              'label' => xarML('Manage Vendors'));
        $menulinks[] = Array('url'   => xarModURL('vendors', 'admin', 'modifyconfig'),
                              'title' => xarML('Modify the configuration settings'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>