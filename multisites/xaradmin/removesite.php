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


function multisites_admin_removesite($args)
{
    if (!xarVarFetch('msid', 'int:1:', $msid)) return;
    if (!xarVarFetch('mssite', 'str:2:', $mssite)) return;
    if (!xarVarFetch('msprefix', 'str:2:', $msprefix)) return;
    if (!xarVarFetch('msdb', 'str:3:', $msdb)) return;
    if (!xarVarFetch('removetables', 'int:1', $removetables,0)) return;
    if (!xarVarFetch('removedatadir', 'int:1', $removedatadir,0)) return;
    if (!xarVarFetch('objectid', 'str:1:', $objectid, NULL, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $btntxt,'', XARVAR_NOT_REQUIRED)) return;
  
    if (!empty($objectid)) {
        $exid = $objectid;
    } 

   // Auth Key
    if (!xarSecConfirmAuthKey()) return;

    // Security
    if (!xarSecurityCheck('DeleteMultisites')) {
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION');
        return;
    }

    if ($removedatadir==1) {
        // remove the site specific data directory tree and files
        // Get site folder name
        $sitedir = xarModAPIFunc('multisites','admin','cleandn', array('sitedn' => $mssite));
        if (!$sitedir) {
            $msg = xarML("Could not clean #(1)", $mssite);
            xarExceptionSet(XAR_USER_EXCEPTION, 'ERROR-CLEANDN', new DefaultUserException($msg));
            return $msg;
        }

        $cWhereIsPerso =xarModGetVar('multisites','masterfolder');
         $sitedirpath=$cWhereIsPerso.'/'.$sitedir['sitedn'].'/';
         $var = is_dir($sitedirpath);
        if ($var) {
            chmod($sitedirpath,0755);
            if (!is_writable($sitedirpath)) {
                $msg = xarML("The subsite directory #(1) could not be deleted!", $sitedirpath);
                xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_WRITEABLE', new DefaultUserException($msg));
                    return $msg;
            } else {
               $removesite= xarModAPIFunc('multisites','admin','recdeldir', array('sitedirpath' => $sitedirpath));
                if (!$removesite){
                    $msg = xarML("The subsite directory #(1) could not be deleted!", $sitedirpath);
                    xarExceptionSet(XAR_USER_EXCEPTION, 'FILE_NOT_WRITEABLE', new DefaultUserException($msg));
                    return $msg;
                }
            }
        }
    }
    // detele the site from the multisite table
    $site = xarModAPIFunc('multisites','admin','delete',
                                 array('msid' => $msid));

    if (!$site) {
        $msg = xarML("Cannot delete subsite '#(1)", $msid);
        xarExceptionSet(XAR_USER_EXCEPTION, 'NO_DATA_RECORD', new DefaultUserException($msg));
        return $msg;
    }
    // remove the site specific tables from the database
    if ($removetables==1) {
      //TO DO
    }
    // success
    xarResponseRedirect(xarModURL('multisites', 'admin', 'view'));
    
    return true;
}
?>