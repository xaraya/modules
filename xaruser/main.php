<?php

function xproject_user_main()
{
    if (!xarSecAuthAction(0, 'xproject::', '::', ACCESS_OVERVIEW)) {
        $msg = xarML('Not authorized to access to #(1)',
                    'xproject');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
	
	$data = xarModAPIFunc('xproject','user','menu');
	$data['welcome'] = xarML('Welcome to the xproject module...');
	return $data;
}

?>