<?php
/**
 * Site Tools Backup package
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/*@function to delete the just completed backup
 *@parameter $bkfile is the name of the backup file
 *
*/
function sitetools_admin_downloaddel ($args)
{
   if (!xarVarFetch('savefile', 'str:1', $savefile,'')) return;

   /* Security checkn*/
   if (!xarSecurityCheck('AdminSiteTools')) return;

   if ((!isset($savefile)) || (empty($savefile))) {
       // Handle the user exceptions yourself
       $status = xarML('The file to delete does not exist.');
       $reason = xarCurrentError();
       if (!empty($reason)) {
          $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
       }
       /* Free the exception to tell Xaraya that you handled it */
       xarErrorFree();
       return $status;
  }
   $info=array();
   /*check the file exists */
   $pathtofile=xarModGetVar('sitetools','backuppath');

  $filetodelete = $pathtofile.'/'.$savefile;

  if (!file_exists($filetodelete)) {
        /* Handle the user exceptions yourself */
       $status = xarML('The file to delete does not exist.');
       $reason = xarCurrentError();
       if (!empty($reason)) {
          $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
       }
       /* Free the exception to tell Xaraya that you handled it */
       xarErrorFree();
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