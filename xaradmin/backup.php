<?php

function accessmethods_admin_backup($args)
{
    extract($args);
    
    if (!xarSecurityCheck('AdminAccessMethods')) {
        return false;
    }
    
    $data = xarModAPIFunc('accessmethods', 'admin', 'menu');
    
    $items = xarModAPIFunc('accessmethods', 'user', 'getall',
                            array('startnum'    => 1,
                                  'numitems'    => -1));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
    
    $data['items'] = $items;
        
	return $data;
}

?>
