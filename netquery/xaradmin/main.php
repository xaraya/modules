<?php

function netquery_admin_main()
{
    $data['helplink'] = Array('url'   => xarML('modules/netquery/xardocs/manual.html'),
                              'title' => xarML('Netquery online administration manual'),
                              'label' => xarML('Online Manual'));

    if (!xarSecurityCheck('EditNetquery')) return;
    if (xarModGetVar('adminpanels', 'overview') == 0) {
        return $data;
    } else {
        xarResponseRedirect(xarModURL('netquery', 'admin', 'modifyconfig'));
    }
    return true;
}
?>
