<?php
//we require the stuff to write and the socket
function xartelnet_userapi_write($args = array('input' => NULL, 'socket' => NULL))
{
    extract($args);
    include_once('modules/xartelnet/telnet.inc.php'); 
    $run =& new telnet;
    if($run) $run->socket = $socket;
    if($run) return $run->write($input);
    return false;
}
?>