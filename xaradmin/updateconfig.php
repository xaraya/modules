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

    if (!xarVarFetch('tab', 'str:1:100', $tab, 'sitecontact_general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('tabmodule', 'str:1:100', $tabmodule, 'sitecontact', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('SupportShortURLs', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
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
    if (!xarVarFetch('useModuleAlias', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultform','int:1:', $defaultform, 1,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage','int:1:', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) {return;}
    if (!xarVarFetch('savedata', 'checkbox', $savedata, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('permissioncheck', 'checkbox', $permissioncheck, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('termslink', 'str:1:', $termslink, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowcc', 'checkbox', $allowcc, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('allowbcc', 'checkbox', $allowbcc, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('adminccs', 'checkbox', $adminccs, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('admincclist', 'str:0:', $admincclist, '', XARVAR_NOT_REQUIRED)) return;

    $allowanoncopy = ($allowcopy && $allowanoncopy)? true :false; //only allow anonymous if allow copy for registered too
    $soptions=array('allowcc'=>$allowcc,
                    'allowbcc'=>$allowbcc,
                    'allowanoncopy'=>$allowanoncopy,
                    'adminccs'=>$adminccs,
                    'admincclist' => $admincclist);

    $soptions=serialize($soptions);
    $scdefaultname=trim($scdefaultname);
    if (!isset($scdefaultname) || $scdefaultname=='') {
        $scdefaultname=xarModVars::get('mail','adminname');
    }
    if ($tab == 'sitecontact_general') {
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
        xarModVars::set('sitecontact', 'defaultform', $defaultform);
        xarModVars::set('sitecontact', 'itemsperpage', $itemsperpage);
        xarModVars::set('sitecontact', 'soptions', $soptions);
        xarModVars::set('sitecontact', 'savedata', $savedata);
        xarModVars::set('sitecontact', 'permissioncheck', $permissioncheck);
        xarModVars::set('sitecontact', 'termslink', trim($termslink));
        xarModVars::set('sitecontact', 'useantibot', $useantibot);
        xarModVars::set('sitecontact', 'scdefaultname', $scdefaultname);

    } else {
        $regid = xarModGetIDFromName($tabmodule);
        xarModItemVars::set('sitecontact', 'customtext', $customtext, $regid);
        xarModItemVars::set('sitecontact', 'customtitle', $customtitle, $regid);
        xarModItemVars::set('sitecontact', 'optiontext', $optiontext, $regid);
        xarModItemVars::set('sitecontact', 'SupportShortURLs', $shorturls, $regid);
        xarModItemVars::set('sitecontact', 'scactive', $scactive, $regid);
        xarModItemVars::set('sitecontact', 'allowcopy', $allowcopy, $regid);
        xarModItemVars::set('sitecontact', 'allowanoncopy', $allowanoncopy, $regid);
        xarModItemVars::set('sitecontact', 'usehtmlemail', $usehtmlemail, $regid);
        xarModItemVars::set('sitecontact', 'webconfirmtext', $webconfirmtext, $regid);
        xarModItemVars::set('sitecontact', 'notetouser', $notetouser, $regid);
        xarModItemVars::set('sitecontact', 'scdefaultemail', $scdefaultemail, $regid);
        xarModItemVars::set('sitecontact', 'scdefaultname', $scdefaultname, $regid);
        xarModItemVars::set('sitecontact', 'defaultform', $defaultform, $regid);
        xarModItemVars::set('sitecontact', 'itemsperpage', $itemsperpage, $regid);
        xarModItemVars::set('sitecontact', 'soptions', $soptions, $regid);
        xarModItemVars::set('sitecontact', 'savedata', $savedata, $regid);
        xarModItemVars::set('sitecontact', 'permissioncheck', $permissioncheck, $regid);
        xarModItemVars::set('sitecontact', 'termslink', trim($termslink), $regid);
        xarModItemVars::set('sitecontact', 'useantibot', $useantibot, $regid);

    }
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModVars::set('sitecontact', 'useModuleAlias', $modulealias);
    } else{
         xarModVars::set('sitecontact', 'useModuleAlias', 0);
    }

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
    xarResponseRedirect(xarModURL('sitecontact', 'admin', 'modifyconfig',array('tabmodule' => $tabmodule, 'tab' => $tab)));


    /* Return true */
    return true;
}

?>
