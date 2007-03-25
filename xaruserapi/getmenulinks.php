<?php

function vendors_userapi_getmenulinks()
{

    if (xarSecurityCheck('ViewVendors',0)) {
        $menulinks[] = Array('url'   => xarModURL('vendors',
                                                  'user',
                                                  'main'),
                              'title' => xarML(''),
                              'label' => xarML(''));
    }
    $menulinks[] = Array('url'   => xarModURL('vendors',
                                              'user',
                                              'filter',
                                              array('objectname' => 'vendors_vendors')),
                          'title' => xarML('View and manage vendors'),
                          'label' => xarML('Vendors'));

    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}

?>
