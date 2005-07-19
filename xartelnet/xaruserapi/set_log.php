<?php
//we require true or false to start logging
function xartelnet_userapi_set_log($s = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->set_log($s);
    return false;
}
?>