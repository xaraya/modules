<?php

function customers_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminCustomers',0)) {
        $menulinks[] = Array('url'   => xarModURL('customers', 'admin', 'customers'),
                              'title' => xarML('Add, edit, delete customers'),
                              'label' => xarML('Manage Customers'));
        $menulinks[] = Array('url'   => xarModURL('customers', 'admin', 'modifyconfig'),
                              'title' => xarML('Modify the configuration settings'),
                              'label' => xarML('Modify Config'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}

?>