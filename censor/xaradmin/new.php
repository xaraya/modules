<?php
/**
 * add new item
 */
function censor_admin_new()
{ 
    // Security Check
    if (!xarSecurityCheck('AddCensor')) return;
    $allowedlocales = xarConfigGetVar('Site.MLS.AllowedLocales');
    foreach($allowedlocales as $locale) {
       $data['locales'][] = array('name' => $locale, 'value' => $locale);
    }
    $data['createlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey(); 
    // Return the output
    //var_dump($data);
    return $data;
} 

?>