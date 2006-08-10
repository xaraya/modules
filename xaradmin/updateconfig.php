<?php
/**
 * Update configuration settings
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage SiteContact Module
 * @link http://xaraya.com/index.php/release/890.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
function sitecontact_admin_updateconfig()
{
    // Confirm authorisation code
    if (!xarSecConfirmAuthKey()) return;

    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('scactive', 'checkbox', $scactive, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useantibot', 'checkbox', $useantibot, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('customtext', 'str:1:', $customtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('customtitle', 'str:1:', $customtitle, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('optiontext', 'str:1:', $optiontext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('webconfirmtext', 'str:1:', $webconfirmtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notetouser', 'str:1:', $notetouser, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowcopy', 'checkbox', $allowcopy, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('usehtmlemail', 'checkbox', $usehtmlemail, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('scdefaultemail', 'str:1:', $scdefaultemail,'', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('scdefaultname', 'str:1:', $scdefaultname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultform','int:1:', $defaultform, 1,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage','int:1:', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('savedata', 'checkbox', $savedata, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permissioncheck', 'checkbox', $permissioncheck, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('termslink', 'str:1:', $termslink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowcc', 'checkbox', $allowcc, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbcc', 'checkbox', $allowbcc, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowanoncopy', 'checkbox', $allowanoncopy, false, XARVAR_NOT_REQUIRED)) return;

    $allowanoncopy = ($allowcopy && $allowanoncopy)? true :false; //only allow anonymous if allow copy for registered too
    $soptions=array('allowcc'=>$allowcc,'allowbcc'=>$allowbcc,'allowanoncopy'=>$allowanoncopy);
    $soptions=serialize($soptions);
    xarModSetVar('sitecontact', 'customtext', $customtext);
    xarModSetVar('sitecontact', 'customtitle', $customtitle);
    xarModSetVar('sitecontact', 'optiontext', $optiontext);
    xarModSetVar('sitecontact', 'SupportShortURLs', $shorturls);
    xarModSetVar('sitecontact', 'scactive', $scactive);
    xarModSetVar('sitecontact', 'allowcopy', $allowcopy);
    xarModSetVar('sitecontact', 'usehtmlemail', $usehtmlemail);
    xarModSetVar('sitecontact', 'webconfirmtext', $webconfirmtext);
    xarModSetVar('sitecontact', 'notetouser', $notetouser);
    xarModSetVar('sitecontact', 'scdefaultemail', $scdefaultemail);
    xarModSetVar('sitecontact', 'scdefaultname', $scdefaultname);
    xarModSetVar('sitecontact', 'defaultform', $defaultform);
    xarModSetVar('sitecontact', 'itemsperpage', $itemsperpage);
    xarModSetVar('sitecontact', 'soptions', $soptions);
    xarModSetVar('sitecontact', 'savedata', $savedata);
    xarModSetVar('sitecontact', 'permissioncheck', $permissioncheck);
    xarModSetVar('sitecontact', 'termslink', trim($termslink));
    xarModSetVar('sitecontact', 'useantibot', $useantibot);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('sitecontact', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('sitecontact', 'useModuleAlias', 0);
    }
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

    $currentalias = xarModGetVar('sitecontact','aliasname');
    $newalias = trim($aliasname);
          /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('sitecontact','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='sitecontact')){
            xarModDelAlias($currentalias,'sitecontact');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'sitecontact');
    }
    /* now set the alias modvar */
    xarModSetVar('sitecontact', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','sitecontact',
              array('module' => 'sitecontact'));

   xarResponseRedirect(xarModURL('sitecontact', 'admin', 'modifyconfig'));

    /* Return true */
    return true;
}

?>