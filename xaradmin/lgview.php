<?php
function netquery_admin_lgview()
{
    if (!xarSecurityCheck('EditNetquery')) return;
    if (!xarVarFetch('startnum', 'isset', $startnum, 1, XARVAR_NOT_REQUIRED)) {return;}
    $data['items'] = array();
    $data['stylesheet'] = xarModGetVar('netquery', 'stylesheet');
    list($data['buttondir']) = split('[._-]', $data['stylesheet']);
    if (!file_exists($data['buttondir'] = 'modules/netquery/xarimages/'.$data['buttondir'])) $data['buttondir'] = 'modules/netquery/xarimages/blbuttons';
    $data['authid'] = xarSecGenAuthKey();
    $routers = xarModAPIFunc('netquery', 'user', 'getlgrouters', array('startnum' => $startnum));
    if (empty($routers))
    {
        $msg = xarML('There are no looking glass routers registered');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }
    for ($i = 0; $i < count($routers); $i++)
    {
        $router = $routers[$i];
        if (xarSecurityCheck('EditNetquery',0))
        {
            $routers[$i]['editurl'] = xarModURL('netquery', 'admin', 'lgmodify', array('router_id' => $router['router_id']));
            $routers[$i]['edittitle'] = xarML('Edit');
        }
        else
        {
            $routers[$i]['editurl'] = '';
            $routers[$i]['edittitle'] = '----';
        }
        if (xarSecurityCheck('DeleteNetquery',0) && $router['router'] != 'default')
        {
            $routers[$i]['deleteurl'] = xarModURL('netquery', 'admin', 'lgdelete', array('router_id' => $router['router_id']));
            $routers[$i]['deletetitle'] = xarML('Delete');
        }
        else
        {
            $routers[$i]['deleteurl'] = '';
            $routers[$i]['deletetitle'] = '------';
        }
    }
    $data['items'] = $routers;
    $data['cfglink'] = Array('url'   => xarModURL('netquery', 'admin', 'config'),
                             'title' => xarML('Return to main configuration'),
                             'label' => xarML('Modify Config'));
    $data['lgvlink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgview'),
                             'title' => xarML('Edit looking glass settings'),
                             'label' => xarML('Edit LG Settings'));
    $data['lgalink'] = Array('url'   => xarModURL('netquery', 'admin', 'lgnew'),
                             'title' => xarML('Add looking glass router'),
                             'label' => xarML('Add LG Router'));
    $data['hlplink'] = Array('url'   => 'modules/netquery/xardocs/manual.html#admin',
                             'title' => xarML('Netquery online manual'),
                             'label' => xarML('Online Manual'));
    return $data;
}
?>