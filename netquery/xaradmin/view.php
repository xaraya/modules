<?php

function netquery_admin_view()
{
    if(!xarSecurityCheck('EditNetquery')) return;
    if(!xarVarFetch('startnum', 'isset',    $startnum, 1,     XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    $data['authid'] = xarSecGenAuthKey();

    $links = xarModAPIFunc('netquery',
                           'admin',
                           'getall',
                           array('startnum' => $startnum,
                                 'numitems' => xarModGetVar('netquery',
                                                            'itemsperpage')));

    if (empty($links)) {
        $msg = xarML('There are no whois links registered');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    for ($i = 0; $i < count($links); $i++) {
        $link = $links[$i];
        if (xarSecurityCheck('EditNetquery',0)) {
            $links[$i]['editurl'] = xarModURL('netquery',
                                              'admin',
                                              'modify',
                                              array('whois_id' => $link['whois_id']));
        } else {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteNetquery',0)) {
            $links[$i]['deleteurl'] = xarModURL('netquery',
                                               'admin',
                                               'delete',
                                               array('whois_id' => $link['whois_id']));
        } else {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }

    $data['items'] = $links;

    $data['addlink'] = Array('url'   => xarModURL('netquery',
                                                  'admin',
                                                  'new'),
                              'title' => xarML('Add a new whois lookup link'),
                              'label' => xarML('Add Whois'));

    return $data;
}
?>