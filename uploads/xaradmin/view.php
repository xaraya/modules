<?php

function uploads_admin_view()
{
    //security check
    if (!xarSecurityCheck('AdminUploads')) return;
    //get filter
    if (!xarVarFetch('filter', 'str:1:', $filter, '', XARVAR_NOT_REQUIRED)) return;
    //get uploads
    $items = xarModAPIFunc('uploads',
                           'admin',
                           'getuploads',
                           array('filter'=>$filter));
      
    // Check for exceptions
    if (!isset($items) && xarExceptionMajor() != XAR_NO_EXCEPTION) return; // throw back
        
    $data['items'] = $items;
    $data['authid'] = xarSecGenAuthKey();
     
    return $data;   
}

?>