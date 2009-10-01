<?php
/**
 * Site Tools Backup package
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools Module
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/*@function to download the just completed backup
 *@parameter $bkfile is the name of the backup file
 *
*/
function sitetools_admin_downloadbkup ($args)
{
    if (!xarVarFetch('savefile', 'str:1', $savefile,'')) return;
 // Security check
 if (!xarSecurityCheck('AdminSiteTools')) return;


  if ((!isset($savefile)) || (empty($savefile))) {
       // Handle the user exceptions yourself
       $status = xarML('The file to download does not exist.');
       $reason = xarCurrentError();
       if (!empty($reason)) {
          $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
       }
       // Free the exception to tell Xaraya that you handled it
       xarErrorFree();
       return $status;
  }


  //check the file exists
 $pathtofile=xarModVars::get('sitetools','backuppath');

  $filetodownload = $pathtofile.'/'.$savefile;

  if (!file_exists($filetodownload)) {
       // Handle the user exceptions yourself
       $status = xarML('The file to download does not exist.');
       $reason = xarCurrentError();
       if (!empty($reason)) {
          $status .= '<br /><br />'. xarML('Reason') .' : '. $reason->toString();
       }
       // Free the exception to tell Xaraya that you handled it
       xarErrorFree();
       return $status;
  }

//  $mimetp=mime_content_type ($filetodownload);

    ob_end_clean();
    // Setup headers for browser

    header("Cache-Control: no-cache, must-revalidate");
    header("Pragma: no-cache");
    header("Pragma: ");
    header("Cache-Control: ");
    header("Content-type: application/octetstream");
//    header("Content-type: ".$mimetp );

    header("Content-disposition: attachment; filename=\"".basename($filetodownload)."\"");
//    header("Content-length: $size");

   $fp = fopen($filetodownload,"rb");
    if( is_resource($fp) )
    {
        while( !feof($fp) )
        {
            echo fread($fp, 1024);
        }
    }
    fclose($fp);

 //ob_end_flush;

   exit();

}
?>