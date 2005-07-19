<?php
//we require the text to stop reading at, like the prompt
function xartelnet_userapi_read($args = array('input' => NULL, 'socket' => NULL))
{
    extract($args);
    if(include_once('modules/xartelnet/telnet.inc.php')) $run =& new telnet;
    //use output buffering to suppress generated errors    
    ob_start();
    $q = '';
    if($run) $run->socket = $socket;
    for ($r = false; $r = $run->read($input);)
	if($r) {
	    $q .= ob_get_contents();
	    ob_end_clean(); flush();
	    $q .= $r;
	    return $q;
	}
	//break;
    }
    return false;
}
?>