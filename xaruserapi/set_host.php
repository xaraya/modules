<?php
//we require the host to connect to
function xartelnet_userapi_set_host($s = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->set_host($s);
    return false;
}
?>