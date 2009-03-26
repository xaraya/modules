<?php
/**
 * LabAffiliate Module - initialization functions
 *
 * @package modules
 * @copyright (C) 2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage LabAffiliate Module
 * @link http://xaraya.com/index.php/release/919
 * @author LabAffiliate Module Development Team
 */
function labaffiliate_admin_updateconfig()
{
    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('displaytitle',    'str::',   $displaytitle, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('default_marketing_copy',    'str::',   $default_marketing_copy, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('inviteonly',  'checkbox', $inviteonly,false,XARVAR_NOT_REQUIRED)) return;

    if (!xarSecConfirmAuthKey()) return;

    xarModSetVar('labaffiliate', 'itemsperpage', $itemsperpage);

    xarModSetVar('labaffiliate', 'inviteonly', $inviteonly);

    xarModSetVar('labaffiliate', 'displaytitle', $displaytitle);

    xarModSetVar('labaffiliate', 'default_marketing_copy', $default_marketing_copy);
    
    xarModSetVar('labaffiliate', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('labaffiliate', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('labaffiliate', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('labaffiliate','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('labaffiliate','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='labaffiliate')){
            xarModDelAlias($currentalias,'labaffiliate');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'labaffiliate');
    }
    /* now set the alias modvar */
    xarModSetVar('labaffiliate', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','labaffiliate',
                   array('module' => 'labaffiliate'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('labaffiliate', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}

?>
