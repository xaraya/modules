<?php
/**
 * Update configuration settings
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
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

    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'sitecontact_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'sitecontact', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('scactive', 'checkbox', $scactive, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('useantibot', 'checkbox', $useantibot, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('customtext', 'str:1:', $customtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('customtitle', 'str:1:', $customtitle, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('optiontext', 'str:1:', $optiontext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('webconfirmtext', 'str:1:', $webconfirmtext, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('notetouser', 'str:1:', $notetouser, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowcopy', 'checkbox', $allowcopy, true, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowanoncopy', 'checkbox', $allowanoncopy, false, XARVAR_NOT_REQUIRED)) return;
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

    $allowanoncopy = ($allowcopy && $allowanoncopy)? true :false; //only allow anonymous if allow copy for registered too
    $soptions=array('allowcc'=>$allowcc,'allowbcc'=>$allowbcc,'allowanoncopy'=>$allowanoncopy);
    $soptions=serialize($soptions);

    if ($data['tab'] == 'sitecontact_general') {
        xarModVars::set('sitecontact', 'customtext', $customtext);
        xarModVars::set('sitecontact', 'customtitle', $customtitle);
        xarModVars::set('sitecontact', 'optiontext', $optiontext);
        xarModVars::set('sitecontact', 'SupportShortURLs', $shorturls);
        xarModVars::set('sitecontact', 'scactive', $scactive);
        xarModVars::set('sitecontact', 'allowcopy', $allowcopy);
        xarModVars::set('sitecontact', 'allowanoncopy', $allowanoncopy);
        xarModVars::set('sitecontact', 'usehtmlemail', $usehtmlemail);
        xarModVars::set('sitecontact', 'webconfirmtext', $webconfirmtext);
        xarModVars::set('sitecontact', 'notetouser', $notetouser);
        xarModVars::set('sitecontact', 'scdefaultemail', $scdefaultemail);
        xarModVars::set('sitecontact', 'scdefaultname', $scdefaultname);
        xarModVars::set('sitecontact', 'defaultform', $defaultform);
        xarModVars::set('sitecontact', 'itemsperpage', $itemsperpage);
        xarModVars::set('sitecontact', 'soptions', $soptions);
        xarModVars::set('sitecontact', 'savedata', $savedata);
        xarModVars::set('sitecontact', 'permissioncheck', $permissioncheck);
        xarModVars::set('sitecontact', 'termslink', trim($termslink));
        xarModVars::set('sitecontact', 'useantibot', $useantibot);
    }
    $regid = xarModGetIDFromName($tabmodule);
        xarModVars::set('sitecontact', 'customtext', $customtext, $regid);
        xarModVars::set('sitecontact', 'customtitle', $customtitle, $regid);
        xarModVars::set('sitecontact', 'optiontext', $optiontext, $regid);
        xarModVars::set('sitecontact', 'SupportShortURLs', $shorturls, $regid);
        xarModVars::set('sitecontact', 'scactive', $scactive, $regid);
        xarModVars::set('sitecontact', 'allowcopy', $allowcopy, $regid);
        xarModVars::set('sitecontact', 'allowanoncopy', $allowanoncopy, $regid);
        xarModVars::set('sitecontact', 'usehtmlemail', $usehtmlemail, $regid);
        xarModVars::set('sitecontact', 'webconfirmtext', $webconfirmtext, $regid);
        xarModVars::set('sitecontact', 'notetouser', $notetouser, $regid);
        xarModVars::set('sitecontact', 'scdefaultemail', $scdefaultemail, $regid);
        xarModVars::set('sitecontact', 'scdefaultname', $scdefaultname, $regid);
        xarModVars::set('sitecontact', 'defaultform', $defaultform, $regid);
        xarModVars::set('sitecontact', 'itemsperpage', $itemsperpage, $regid);
        xarModVars::set('sitecontact', 'soptions', $soptions, $regid);
        xarModVars::set('sitecontact', 'savedata', $savedata, $regid);
        xarModVars::set('sitecontact', 'permissioncheck', $permissioncheck, $regid);
        xarModVars::set('sitecontact', 'termslink', trim($termslink), $regid);
        xarModVars::set('sitecontact', 'useantibot', $useantibot, $regid);

    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModVars::set('sitecontact', 'useModuleAlias', $modulealias);
    } else{
         xarModVars::set('sitecontact', 'useModuleAlias', 0);
    }
    $scdefaultemail=trim($scdefaultemail);
    if ((!isset($scdefaultemail)) || $scdefaultemail=='') {
       $scdefaultemail=xarModVars::get('mail','adminmail');
    }
    xarModVars::set('sitecontact', 'scdefaultemail', $scdefaultemail);

    $scdefaultname=trim($scdefaultname);

    if (!isset($scdefaultname) || $scdefaultname=='') {
       $scdefaultname=xarModVars::get('mail','adminname');
    }

    xarModVars::set('sitecontact', 'scdefaultname', $scdefaultname);

    $currentalias = xarModVars::get('sitecontact','aliasname');
    $newalias = trim($aliasname);
          /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModVars::get('sitecontact','useModuleAlias');

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
    xarModVars::set('sitecontact', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','sitecontact',
              array('module' => 'sitecontact'));
    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $data['tab'])));


    /* Return true */
    return true;
}

?>