<?php
/*
 * Xaraya Multisites
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 - 2009by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Multisites Module
 * @author
 */

function multisites_admin_delete($args)
{
    extract($args);
    if (!xarVarFetch('msid', 'int:2:', $msid)) return;

    // Security check
    if (!xarSecurityCheck('DeleteMultisites')) {
        return;
    }
      // Check if the Master site has been set up
    $lIsMultisites = xarConfigGetVar('System.MS.MultiSites');
    $lIsMaster=xarConfigGetVar('System.MS.Master');
    $masterurl=xarModGetVar('multisites','masterurl');
    $servervar=xarModGetVar('multisites','servervar');
    $currenthost=$_SERVER[$servervar];

     if (($lIsMultisites==1) and ($lIsMaster==1) and ($currenthost==$masterurl)){
       // This is the master.

       $data['authid']     = xarSecGenAuthKey();
       $data['mastersite'] = true;
//       $data['msid']     = $msid;

       $subsite =xarModAPIFunc('multisites','user','get',
                             array('msid' => $msid));

       if (!$subsite) {
            $msg = xarML("Cannot delete subsite '".$siteDB);
            xarErrorSet(XAR_USER_EXCEPTION, 'NO_DATA_RECORD', new DefaultUserException($msg));
            return $msg;
       }

       $data['msid']          = $subsite['msid'];
       $data['mssite']        = $subsite['mssite'];
       $data['msprefix']      = $subsite['msprefix'];
       $data['msdb']          = $subsite['msdb'];
       $data['msshare']       = $subsite['msshare'];
       $data['removetables']  = 1;
       $data['removedatadir'] = 0;


   } else {

      $data['mastersite']= false;
      $data['infomsg']=xarML('This is not the Master site or it is not configured.');
   }
    // Return the template variables defined in this function
    return $data;
}
?>
