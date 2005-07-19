<?php

function xartelnet_adminapi_getmenulinks()
{
    if(xarSecurityCheck('AdminXarTelnet')) {
        $menulinks[] = Array('url'   => xarModURL('xartelnet',
                                                  'admin',
                                                  'modifyconfig'),
                             'title' => xarML('Modify the configuration for the module'),
                             'label' => xarML('Modify Config'));

        $menulinks[] = Array('url'   => xarModURL('xartelnet',
                                                  'admin',
                                                  'connecttest'),
                             'title' => xarML('Test the settings'),
                             'label' => xarML('Test Connection'));
    } else {
        $menulinks = '';
    }
    return $menulinks;
}
?>