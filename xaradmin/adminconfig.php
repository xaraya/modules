<?php
// File: $Id$
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */

/**
 * Function to set administrative options for multisites
 * Currently only to set listing of sites per page in view mode
 */
function multisites_admin_adminconfig()
{
      // Security check
    if (!xarSecurityCheck('AdminMultisites')) {
           return;
    }
   $data['authid'] = xarSecGenAuthKey();

    // Return the template variables defined in this function
    return $data;

}
?>
