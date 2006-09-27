<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author XProject module development team
 */
function xproject_admin_updateconfig()
{
    if (!xarSecurityCheck('AdminXProject')) return;
    if (!xarVarFetch('tab', 'str:1:100', $tab, 'general', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('returnurl', 'str:1:100', $returnurl, xarModURL('xproject', 'admin', 'main'), XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;
    
    switch($tab) {
        case 'hooks':
            // Role type 'user' (itemtype 0).
            xarModCallHooks('module', 'updateconfig', 'xproject',
                            array('module' => 'xproject',
                                  'itemtype' => 0));
            break;
        case 'general':
            if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 20, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('staffcategory', 'int',      $staffcategory, 0, XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('clientcategory', 'int',      $clientcategory, 0, XARVAR_NOT_REQUIRED)) return;
            
            if (!xarVarFetch('websiteprojecttype', 'str',      $websiteprojecttype, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('draftstatus', 'str',      $draftstatus, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('activestatus', 'str',      $activestatus, '', XARVAR_NOT_REQUIRED)) return;
            if (!xarVarFetch('archivestatus', 'str',      $archivestatus, '', XARVAR_NOT_REQUIRED)) return;
            
            xarModSetVar('xproject', 'itemsperpage', $itemsperpage);
            xarModSetVar('xproject', 'staffcategory', $staffcategory);
            xarModSetVar('xproject', 'clientcategory', $clientcategory);
            
            xarModSetVar('xproject', 'websiteprojecttype', $websiteprojecttype);
            xarModSetVar('xproject', 'draftstatus', $draftstatus);
            xarModSetVar('xproject', 'activestatus', $activestatus);
            xarModSetVar('xproject', 'archivestatus', $archivestatus);
            
            break;
    }
/*
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('xproject', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('xproject', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('xproject','aliasname');
    $newalias = trim($aliasname);

    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('xproject','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        if (isset($hasalias) && ($hasalias =='xproject')){
            xarModDelAlias($currentalias,'xproject');
        }
        xarModSetAlias($newalias,'xproject');
    }
*/

    xarResponseRedirect($returnurl);

    return true;
}

?>
