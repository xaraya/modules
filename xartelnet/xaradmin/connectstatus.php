<?php
function xartelnet_admin_connectstatus()
{
    if(!xarSecConfirmAuthKey()) return false;
    if(!xarSecurityCheck('AdminXarTelnet')) return;
    $data['authid'] = xarSecGenAuthKey();
    $data['results'] = '';
    if($socket = xarmodapifunc('xartelnet','user','connect', NULL))
    {
	$w = "GET / HTTP/1.0\n\n";
	xarmodapifunc('xartelnet','user','write',array('input' => $w, 'socket' => $socket));
	$r = xarmodapifunc('xartelnet','user','read',array('input' => xarmodgetvar('xartelnet','prompt'), 'socket' => $socket));
	$data['results'] .= '</pre>'.$r.'</pre>';
	xarmodapifunc('xartelnet','user','disconnect',$socket);
    } else {
	$data['results'] .= xarVarPrepForDisplay("Not Successful");
    }
    return $data;
}
?>