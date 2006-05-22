<?php
/**
 * Standard function to update module configuration parameters
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */

/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author ITSP module development team
 */
function itsp_admin_updateconfig()
{
    if (!xarVarFetch('OverrideSV',   'checkbox', $OverrideSV, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('UseStatusVersions',  'checkbox', $UseStatusVersions,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.
     */
    xarModSetVar('itsp', 'OverrideSV', $OverrideSV);
    xarModSetVar('itsp', 'UseStatusVersions', $UseStatusVersions);
    xarModSetVar('itsp', 'officemail', $officemail);
    xarModSetVar('itsp', 'itemsperpage', $itemsperpage);
    xarModSetVar('itsp', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('itsp', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('itsp', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('itsp','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('itsp','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='itsp')){
            xarModDelAlias($currentalias,'itsp');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'itsp');
    }
    /* now set the alias modvar */
    xarModSetVar('itsp', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','itsp',
                   array('module' => 'itsp'));
    xarSessionSetVar('statusmsg', xarML('Module configuration was successfully updated!'));
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('itsp', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>