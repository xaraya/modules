<?php
/*
 * Censor Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
 * @author John Cox
*/

/**
 * add new censored word
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

    return $data;
} 

?>