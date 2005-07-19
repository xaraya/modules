<?php
//we require the socket to close
function xartelnet_userapi_disconnect($socket = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->disconnect($socket);
    return false;
}
?>