<?php
/**
 * Standard function to update module configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 */

/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author Example module development team
 */
function surveys_admin_updateconfig()
{
    /* Get parameters from whatever input we need.
     */
    if (!xarVarFetch('SendEventMails', 'checkbox', $SendEventMails, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    /* Confirm authorisation code.*/

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.  Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModSetVar('surveys', 'SendEventMails', $SendEventMails);
    xarModSetVar('surveys', 'itemsperpage', $itemsperpage);
    xarModSetVar('surveys', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('surveys', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('surveys', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('surveys','aliasname');
    $newalias = trim($aliasname);
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('surveys','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='surveys')){
            xarModDelAlias($currentalias,'surveys');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'surveys');
    }
    /* now set the alias modvar */
    xarModSetVar('surveys', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','surveys',
                   array('module' => 'surveys'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('surveys', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>