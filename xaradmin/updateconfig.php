<?php
/**
 * File: $Id$
 * 
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
  * @subpackage SiteContact
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 */
function sitecontact_admin_updateconfig()
{
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('customtext', 'str:1:', $customtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('customtitle', 'str:1:', $customtitle, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('optiontext', 'str:1:', $optiontext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('webconfirmtext', 'str:1:', $webconfirmtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notetouser', 'str:1:', $notetouser, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowcopy', 'checkbox', $allowcopy, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usehtmlemail', 'checkbox', $usehtmlemail, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('scdefaultemail', 'str:1:', $scdefaultemail,'', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('scdefaultname', 'str:1:', $scdefaultname, '', XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;
    xarModSetVar('sitecontact', 'customtext', $customtext);
    xarModSetVar('sitecontact', 'customtitle', $customtitle);
    xarModSetVar('sitecontact', 'optiontext', $optiontext);
    xarModSetVar('sitecontact', 'SupportShortURLs', $shorturls);
    xarModSetVar('sitecontact', 'allowcopy', $allowcopy);
    xarModSetVar('sitecontact', 'usehtmlemail', $usehtmlemail);
    xarModSetVar('sitecontact', 'webconfirmtext', $webconfirmtext);
    xarModSetVar('sitecontact', 'notetouser', $notetouser);
    xarModSetVar('sitecontact', 'scdefaultemail', $scdefaultemail);
    xarModSetVar('sitecontact', 'scdefaultname', $scdefaultname);    

    $scdefaultemail=trim($scdefaultemail);
    if ((!isset($scdefaultemail)) || $scdefaultemail=='') {
       $scdefaultemail=xarModGetVar('mail','adminmail');
    }
    xarModSetVar('sitecontact', 'scdefaultemail', $scdefaultemail);

    $scdefaultname=trim($scdefaultname);

    if (!isset($scdefaultname) || $scdefaultname=='') {
       $scdefaultname=xarModGetVar('mail','adminname');
    }

   xarModSetVar('sitecontact', 'scdefaultname', $scdefaultname);

    xarModCallHooks('module','updateconfig','sitecontact',
              array('module' => 'sitecontact'));

   xarResponseRedirect(xarModURL('sitecontact', 'admin', 'modifyconfig'));

    // Return
    return true;
}

?>
