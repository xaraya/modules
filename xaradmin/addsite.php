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
 * add a new site (domain or subdomain)
 */
function multisites_admin_addsite()
{
   global $HTTP_SERVER_VARS;
  // Security check
    if (!xarSecurityCheck('AddMultisites')) {
        return;
    }
      // Check if the Master site has been set up
    $lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
    $lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];

     if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){

       // This is the master, and Master site has been set up
       $data['items'] = array();
       $data['authid']     = xarSecGenAuthKey();
       $data['mastersite'] = true;
       $data['siteDN']     = '';
       $data['sharedTables']  = '';// prefix sharing, defaults to site
       $data['msPrefix']   = ''; // Site table prefix
       $data['siteDB']     = xarDBGetName(); // database to be used, defaults to Master
       $data['createdb']   = false;

        // Select item values (site status).
       // Default is a 'pending' state. So no need to display when creating the new site
       $data['siteStatus'] = array(
                 'Pending'  => xarML('Pending'),
                 'Active'   => xarML('Active'),
                 'Inactive' => xarML('Inactive'));

      //TO DO: maybe add some extras later when I think of what

   } else {  //this is not the Master, or Master site not configured
      $data['mastersite']= false;
   }
    // Return the template variables defined in this function
    return $data;
}
?>
