<?php
/*
 * File: $Id: $
 *
 * Newsletter 
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003-2004 by the Xaraya Development Team
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * Add new Newsletter disclaimer
 *
 * @public
 * @author Richard Cave
 * @returns array
 * @return $data
 */
function newsletter_admin_newdisclaimer()
{
    // Security check
    if(!xarSecurityCheck('AddNewsletter')) return;

    // Get the admin menu
    $data = xarModAPIFunc('newsletter', 'admin', 'menu');

    // Set template labels
    $data['addlabel'] = xarVarPrepForDisplay(xarML('Add Disclaimer'));

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;
}

?>
