<?php
function authinvision2_adminapi_getmenulinks()
{
    // Security check 
    if(xarSecurityCheck('Adminauthinvision2')) {
        $menulinks[] = Array('url'   => xarModURL('authinvision2',
                                                   'admin',
                                                   'modifyconfig'),
                              'title' => xarML('Modify the configuration for the
 module'),
                              'label' => xarML('Modify Config'));
    } else {
        $menulinks = '';
    }

    return $menulinks;
}
?>
