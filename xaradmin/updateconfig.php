<?php
/**
 * Standard function to update module configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author jojodee
 */
function legis_admin_updateconfig()
{
    /* Get parameters from whatever input we need.  All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed.  Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('bold',         'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('docname',    'str:1:',   $docname, 'Legislation', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaultmaster', 'int:0:', $defaultmaster,1,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('defaulthall', 'int:0:', $defaulthall,1,XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('moderatorgroup', 'int:0:', $moderatorgroup,NULL,XARVAR_NOT_REQUIRED)) return;
   if (!xarVarFetch('allowchange', 'checkbox', $allowchange,false,XARVAR_NOT_REQUIRED)) return;
    /* Confirm authorisation code.  This checks that the form had a valid
     * authorisation code attached to it.  If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables.  Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModSetVar('legis', 'bold', $bold);
    xarModSetVar('legis', 'docname', $docname);    
    xarModSetVar('legis', 'itemsperpage', $itemsperpage);
    xarModSetVar('legis', 'SupportShortURLs', $shorturls);
    xarModSetVar('legis', 'defaultmaster', $defaultmaster);
    xarModSetVar('legis', 'defaulthall', $defaulthall);
    xarModSetVar('legis', 'moderatorgroup', $moderatorgroup);
    xarModSetVar('legis', 'allowhallchange', $allowchange);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('legis', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('legis', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('legis','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('legis','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='legis')){
            xarModDelAlias($currentalias,'legis');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'legis');
    }
    /* now set the alias modvar */
    xarModSetVar('legis', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','legis',
                   array('module' => 'legis'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('legis', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>
