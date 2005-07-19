<?php
//we require the port
function xartelnet_userapi_set_port($s = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->set_port($s);
    return false;
}
?>