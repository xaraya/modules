<?php
function netquery_admin_main()
{
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Configuration'));
    $data['wivlink'] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('View-edit whois lookup links'),
                             'label' => xarML('Edit Whois Links'));
    $data['lgvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('View-edit looking glass routers'),
                             'label' => xarML('Edit LG Routers'));
    $data['ptvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('View-edit looking glass routers'),
                             'label' => xarML('Edit Port Services'));
    $data['hlplink'] = Array('url'   => xarML('modules/netquery/xardocs/manual.html#admin'),
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    if (!xarSecurityCheck('AdminNetquery')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return $data;
    } else {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
    }
    return $data;
}
?>