<?php
function netquery_admin_wiview()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('startnum', 'isset', $startnum, 1, XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    list($data['buttondir']) = preg_split('/[._-]/', $data['stylesheet']);
    if (!file_exists($data['buttondir'] = 'modules/netquery/xarimages/'.$data['buttondir'])) $data['buttondir'] = 'modules/netquery/xarimages/blbuttons';
    $data['authid'] = xarSecGenAuthKey();
    $links = xarModAPIFunc('netquery', 'user', 'getlinks', array('startnum' => $startnum));
    if (empty($links))
    {
        $msg = xarML('There are no whois links registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    for ($i = 0; $i < count($links); $i++)
    {
        $link = $links[$i];
        if (xarSecurityCheck('EditNetquery',0))
        {
            $links[$i]['editurl'] = xarModURL('netquery', 'admin', 'wimodify', array('whois_id' => $link['whois_id']));
        }
        else
        {
            $links[$i]['editurl'] = '';
        }
        $links[$i]['edittitle'] = xarML('Edit');
        if (xarSecurityCheck('DeleteNetquery',0))
        {
            $links[$i]['deleteurl'] = xarModURL('netquery', 'admin', 'widelete', array('whois_id' => $link['whois_id']));
        }
        else
        {
            $links[$i]['deleteurl'] = '';
        }
        $links[$i]['deletetitle'] = xarML('Delete');
    }
    $data['items'] = $links;
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Config'));
    $data['wivlink'] = Array('url'   => xarModURL('netquery', 'admin', 'wiview'),
                             'title' => xarML('Edit whois lookup links'),
                             'label' => xarML('Edit Whois'));
    $data['wialink'] = Array('url'   => xarModURL('netquery', 'admin', 'winew'),
                             'title' => xarML('Add whois lookup link'),
                             'label' => xarML('Add Whois'));
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>