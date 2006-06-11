<?php
function netquery_adminapi_getmenulinks()
{
    if (xarSecurityCheck('AdminNetquery', 0)) {
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Modify configuration settings'),
                             'label' => xarML('Modify Configuration'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('View-edit whois TLD/server links'),
                             'label' => xarML('Edit Whois TLDs'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('View-edit port services/exploits'),
                             'label' => xarML('Edit Port Services'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'flview'),
                             'title' => xarML('View-edit service category flags'),
                             'label' => xarML('Edit Category Flags'));
        $menulinks[] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('View-edit looking glass routers'),
                             'label' => xarML('Edit LG Routers'));
    }
    if (empty($menulinks)){
        $menulinks = '';
    }
    return $menulinks;
}
?>