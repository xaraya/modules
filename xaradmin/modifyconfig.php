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
 * modify module configuration
 */
function censor_admin_modifyconfig()
{ 
    // Security Check
    if (!xarSecurityCheck('AdminCensor')) return;
    $data['authid'] = xarSecGenAuthKey();
    $data['submitlabel'] = xarML('Submit');
    $data['itemsperpage'] = xarModGetVar('censor', 'itemsperpage');
    $data['replace'] = xarModGetVar('censor', 'replace');
    return $data;
} 
?>