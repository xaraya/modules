<?php
/*
 * File: $Id:
 *
 * Delete a downloaded file from the server
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteTools module
 * @author jojodee <jojodee@xaraya.com>
*/

/*@function to deletethe just completed backup
 *@parameter $bkfile is the name of the backup file
 *
*/
function sitetools_admin_downloaddel ($args)
{
   if (!xarVarFetch('savefile', 'str:1', $savefile,'')) return;

   // Security check
   if (!xarSecurityCheck('AdminSiteTools')) return;

   if ((!isset($savefile)) || (empty($savefile))) {
       // Handle the user exceptions yourself
       $status = xarML('The file to delete does not exist.');
       $reason = xarExceptionValue();
       if (!empty($reason)) {
          $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
       }
       // Free the exception to tell Xaraya that you handled it
       xarExceptionFree();
       return $status;
  }
   $info=array();
   //check the file exists
   $pathtofile=xarModGetVar('sitetools','backuppath');

  $filetodelete = $pathtofile.'/'.$savefile;

  if (!file_exists($filetodelete)) {
        // Handle the user exceptions yourself
       $status = xarML('The file to delete does not exist.');
       $reason = xarExceptionValue();
       if (!empty($reason)) {
          $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
       }
       // Free the exception to tell Xaraya that you handled it
       xarExceptionFree();
       return $status;
  }


 $filedeleted=unlink($filetodelete);
 if ($filedeleted) {
        $info['outcome']=true;

    } else {
       	$info['outcome']=false;
    }

 $info['filedeleted']=$filetodelete;

return $info;
}
?>
