<?php
function netquery_admin_portlist()
{
    if(!xarSecurityCheck('OverviewNetquery')) return;
    if (!xarVarFetch('portnum', 'int:1:100000', $portnum, '80', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    $data['ports'] = array();
    $ports = xarModAPIFunc('netquery', 'user', 'getportdata', array('port' => $portnum));
    $data['ports'] = $ports;
    $data['authid'] = xarSecGenAuthKey();
    $data['portnum'] = $portnum;
    return $data;
}
?>