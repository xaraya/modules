<?php
/**
 * Update configuration parameters of the module with information passed back by the modification form
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls Module
 * @link http://xaraya.com/index.php/release/247.html
 * @author Maxercalls module development team
 */
/**
 * Update the configuration of the module
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form.
 * Main item is the category for the calls
 *
 * @param int itemsperpage
 * @author MichelV
 * @return bool with redirect
 */
function maxercalls_admin_updateconfig()
{

    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;
    // Update module variables.
    xarModSetVar('maxercalls', 'itemsperpage', $itemsperpage);
    xarModSetVar('maxercalls', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('maxercalls', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('maxercalls', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('maxercalls','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('maxercalls','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='maxercalls')){
            xarModDelAlias($currentalias,'maxercalls');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'maxercalls');
    }
    /* now set the alias modvar */
    xarModSetVar('maxercalls', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','maxercalls',
                   array('module' => 'maxercalls',
                         'itemtype' => 1));

    // This function generated no output, and so now it is complete we redirect
    // the user to an appropriate page for them to carry on their work
    xarResponseRedirect(xarModURL('maxercalls', 'admin', 'modifyconfig'));

    // Return
    return true;
}
?>
