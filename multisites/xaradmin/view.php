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
 * Standard function to list and view existing subsites
 * Options to inactivate, delete and edit subsites
 * Multisite configuration in _admin_modifyconfig() must be set to continue here
 */
function multisites_admin_view()
{
global $HTTP_SERVER_VARS;

    // Get parameters from whatever input we need
    if (!xarVarFetch('startnum', 'str:1:', $startnum, '1', XARVAR_NOT_REQUIRED)) return;

    // Set up an array for item data
    $data['items'] = array();

    // Check if the Master site has been set up
    $lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
    $lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];
     if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
    // The master multisite has been configured and this is the master - continue
        $data['mastersite']= true;
        $data['authid'] = xarSecGenAuthKey();

        // Call the xarTPL helper function to produce a pager in case of there
        // being many items to display.
        $data['pager'] = xarTplGetPager($startnum,
              xarModAPIFunc('multisites', 'user', 'countitems'),
              xarModURL('multisites', 'admin', 'view', array('startnum' => '%%')),
              xarModGetVar('multisites', 'itemsperpage'));

        // Security Check
        if(!xarSecurityCheck('AdminMultisites')) return;

        // Labels for display
        $data['sitelabel']    = xarVarPrepForDisplay(xarML('Site Name'));
        $data['prefixlabel']  = xarVarPrepForDisplay(xarML('Table Prefix'));
        $data['dblabel']      = xarVarPrepForDisplay(xarML('Database'));
        $data['sharelabel']   = xarVarPrepForDisplay(xarML('Shared'));
        $data['statuslabel']  = xarVarPrepForDisplay(xarML('Status'));
        $data['optionslabel'] = xarVarPrepForDisplay(xarML('Options'));

       // The user API function is called
       $sites = xarModAPIFunc('multisites',
                              'user',
                              'getall',
                        array('startnum' => $startnum,
                             'numitems' => xarModGetVar('multisites',
                                                       'itemsperpage')));
       $data['siteno']=count($sites);
        if (empty($sites)) {
           $msg = xarML('No sites in database.', 'multisites');
           xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
           return;
       }

       // Check individual permissions for Edit/Delete/Enable
       $authid = xarSecGenAuthKey();

       for ($i = 0; $i < count($sites); $i++) {
           $site = $sites[$i];
         if (xarSecurityCheck('EditMultisites', 0, 'Item', "$site[mssite]:All:$site[msid]")) {
               $sites[$i]['enableurl'] = xarModURL('multisites',
                   'admin',
                   'changestatus',
                   array('msid' => $site['msid']));
           } else {
               $sites[$i]['enableurl'] = '';
           }
           $sites[$i]['enablelabel'] = xarML('Change Status');
           if (xarSecurityCheck('EditMultisites', 0, 'Site', "$site[mssite]:All:$site[msid]")) {
               $sites[$i]['editurl'] = xarModURL('multisites',
                   'admin',
                   'modify',
                   array('msid' => $site['msid']));
           } else {
               $sites[$i]['editurl'] = '';
           }
           $sites[$i]['editlabel'] = xarML('Configure');
           if (xarSecurityCheck('DeleteMultisites', 0, 'site', "$site[mssite]:All:$site[msid]")) {
            $sites[$i]['deleteurl'] = xarModURL('multisites',
                                                    'admin',
                                                   'delete',
                                             array('msid' => $site['msid']));
           } else {
            $sites[$i]['deleteurl'] = '';
           }
           $sites[$i]['deletelabel'] = xarML('Delete');
        }
           // Add the array of items to the template variables

          $data['items'] = $sites;
          $data['masterurl'] =$masterurl;

    } else {  // The master site has not been configured, or this is not the master
      $data['mastersite']= false;
    }

    // Return the template variables defined in this function
    return $data;
}
?>
