<?php
/**
 * Site Tools Template Cache Management
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

/**
 * Clear cache files
 * @author jojodee
 * @param  $ 'confirm' confirm that this item can be deleted
 */
function sitetools_admin_deletecache($args)
{
    // Get parameters from whatever input we need.
    if (!xarVarFetch('delrss', 'checkbox', $delrss, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('delado', 'checkbox', $delado, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('deltempl', 'checkbox', $deltempl, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('confirm', 'str:1:', $confirm, '', XARVAR_NOT_REQUIRED)) return;

    /* Security check - important to do this as early as possible */
    if (!xarSecurityCheck('DeleteSiteTools')) {
        return;
    }
    $data[]=array();
    /* Check for confirmation. */
    if (empty($confirm)) {
        /* No confirmation yet - display a suitable form to obtain confirmation
         * of this action from the user
         */

        $data = xarModAPIFunc('sitetools', 'admin', 'menu');
        $data['adopath']   = xarModGetVar('sitetools','adocachepath');
        $data['rsspath']   = xarModGetVar('sitetools','rsscachepath');
        $data['templpath'] = xarModGetVar('sitetools','templcachepath');
        $data['delado']    = 0;
        $data['delrss']    = 0;
        $data['deltempl']  = 0;
        $data['delfin']    = false;
        /* Generate a one-time authorisation code for this operation */
        $data['authid'] = xarSecGenAuthKey();
        /*Return the template variables defined in this function */
        return $data;
    }
    /* If we get here it means that the user has confirmed the action */

    /* Confirm authorisation code. */
    if (!xarSecConfirmAuthKey()) return;
  if ($delado || $delrss || $deltempl) {
    if ($delado==1) {
        /* recursively delete all adodb cache files
         * Get site folder name
         */
        $adopath = xarModGetVar('sitetools','adocachepath');

        $var = is_dir($adopath);
        if ($var) {
            if (!is_writable($adopath)) {
                $msg = xarML("The ADODB cache directory and files in #(1) could not be deleted!", $adopath);
                xarErrorSet(XAR_USER_EXCEPTION, 'FILE_NOT_WRITEABLE', new DefaultUserException($msg));
                    return $msg;
            } else { /* making a few assumptions about structure of adodb cache subdirs and files here */
                $handle=opendir($adopath);
                $skip_array = array('.','..','SCCS','index.htm','index.html');
                while (false !== ($file = readdir($handle))) {
                    if (!in_array($file,$skip_array)) {
                        $subhandle=opendir("{$adopath}/{$file}");
                        /* iansym::these are the files we do not want to delete */
                        $skip_array2 = array('.','..','SCCS');
                        while (false !== ($sfile = readdir($subhandle))) {
                            /* check the skip array and delete files that are not in it */
                            if(!in_array($sfile,$skip_array2)) {
                                unlink("{$adopath}/{$file}/{$sfile}");
                            }
                        }
                        closedir($subhandle);
                        rmdir("{$adopath}/{$file}");
                    }
                }
                closedir($handle);
                $data['delfin']    = true;
            }
        }
     }
     if ($delrss==1) {
        /* delete all rss cache files */
        /* Get site folder name */
        $rsspath = xarModGetVar('sitetools','rsscachepath');

        $var = is_dir($rsspath);
        if ($var) {
            /*chmod($templpath,0755); path should already be writable */
            if (!is_writable($rsspath)) {
                $msg = xarML("The RSS cache directory and files in #(1) could not be deleted!", $rsspath);
                xarErrorSet(XAR_USER_EXCEPTION, 'FILE_NOT_WRITEABLE', new DefaultUserException($msg));
                    return $msg;
             } else {
                $handle=opendir($rsspath);
                /* iansym::these are the files we do not want to delete */
                $skip_array = array('.','..','SCCS','index.htm','index.html');
                while (false !== ($file = readdir($handle))) {
                  /* check the skip array and delete files that are not in it */
                  if (!in_array($file,$skip_array)) {
                    unlink($rsspath."/".$file);/* delete the file */
                  }
                }
                closedir($handle);
                $data['delfin']    = true;
            }
        }
     }

     if ($deltempl==1) {
        /*  delete all template cache files
         * Get site folder name
         */
        $templpath = xarModGetVar('sitetools','templcachepath');

        $var = is_dir($templpath);
        if ($var) {
            /*chmod($templpath,0755); ath should already be writable */
            if (!is_writable($templpath)) {
                $msg = xarML("The Template cache directory and files in #(1) could not be deleted!", $templpath);
                xarErrorSet(XAR_USER_EXCEPTION, 'FILE_NOT_WRITEABLE', new DefaultUserException($msg));
                    return $msg;
            } else {
                $handle=opendir($templpath);
                /* iansym::these are the files we do not want to delete */
                $skip_array = array('.','..','index.htm','index.html','SCCS');
                while (false !== ($file = readdir($handle))) {
                  /* check the skip array and delete files that are not in it */
                  if(!in_array($file,$skip_array)) {
                    unlink($templpath."/".$file); /* delete this file */
                  }
                }
                closedir($handle);
                $data['delfin']    = true;
            }
        }
     }
     return $data;
    }
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work */
   xarResponseRedirect(xarModURL('sitetools', 'admin', 'deletecache'));

    return true;
}
?>