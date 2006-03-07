<?php
function window_adminapi_getmenulinks()
{

// Security Check
    if (xarSecurityCheck('AdminWindow',0)) {
        $menulinks[] = Array('url'   => xarModURL('window',
                                                  'admin',
                                                  'general'),
                              'title' => xarML('General Settings'),
                              'label' => xarML('General Settings'));
    }

    if (xarSecurityCheck('AdminWindow',0)) {
        $menulinks[] = Array('url'   => xarModURL('window',
                                                  'admin',
                                                  'addurl'),
                              'title' => xarML('Specific Settings'),
                              'label' => xarML('Specific Settings'));
    }

    if (empty($menulinks)){
        $menulinks = '';
    }

    return $menulinks;
}
?>