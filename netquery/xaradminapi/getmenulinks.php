<?php
function netquery_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Modify configuration settings'),
                             'label' => xarML('Modify Configuration'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('View-edit whois lookup links'),
                             'label' => xarML('Edit Whois Links'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('View-edit looking glass routers'),
                             'label' => xarML('Edit LG Routers'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('View-edit port services data'),
                             'label' => xarML('Edit Port Services'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>