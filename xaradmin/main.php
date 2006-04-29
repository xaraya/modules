<?php
function netquery_admin_main()
{
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Configuration'));
    $data['wivlink'] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('View-edit whois TLD/server links'),
                             'label' => xarML('Edit Whois Links'));
    $data['ptvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'ptview'),
                             'title' => xarML('View-edit port services/exploits'),
                             'label' => xarML('Edit Port Services'));
    $data['flvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'flview'),
                             'title' => xarML('View-edit service category flags'),
                             'label' => xarML('Edit Category Flags'));
    $data['lgvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('View-edit looking glass routers'),
                             'label' => xarML('Edit LG Routers'));
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    if (!xarSecurityCheck('AdminNetquery')) return;

    xarResponseRedirect(xarModURL('netquery', 'admin', 'config'));
    
    return $data;
}
?>