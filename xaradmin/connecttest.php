<?php
function xartelnet_admin_connecttest()
{
    if(!xarSecurityCheck('AdminXarTelnet')) return;
    $data['authid'] = xarSecGenAuthKey();
    include_once 'modules/xartelnet/telnet.inc.php';
    $run =& new telnet;
    $run->set_defaults();
    $data['host'] = xarVarPrepForDisplay($run->host);
    $data['port'] = xarVarPrepForDisplay($run->port);
    $data['timeout'] = xarVarPrepForDisplay($run->timeout);
    return $data;
}
?>