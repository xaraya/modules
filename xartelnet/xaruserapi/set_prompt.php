<?php
//we require the prompt to stop reading at
function xartelnet_userapi_set_prompt($s = NULL)
{
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    if($run) return $run->set_prompt($s);
    return false;
}
?>