<?php
//we require a time in seconds
function xartelnet_userapi_set_timeout($s = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->set_timeout($s);
    return false;
}
?>