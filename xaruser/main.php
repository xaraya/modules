<?php

function xproject_user_main()
{
    if (!xarSecurityCheck('ViewXProject')){
        return;
    }
	
	$data = xarModAPIFunc('xproject','user','menu');
	$data['welcome'] = xarML('Welcome to the xproject module...');
	return $data;
}
?>