<?php
//we require only the resource id of the open connection, or NULL if creating a new connection
function xartelnet_userapi_connect($socket = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->connect($socket);
    return false;
}
?>