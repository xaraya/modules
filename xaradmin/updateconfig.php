<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 */

/**
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author Example module development team
 */
function example_admin_updateconfig()
{
    /* Get parameters from whatever input we need.  All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed.  Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('bold', 'checkbox', $bold, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('itemsperpage', 'int', $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls', 'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname', 'str:1:', $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias','checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

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
    xarModSetVar('example', 'bold', $bold);
    xarModSetVar('example', 'itemsperpage', $itemsperpage);
    xarModSetVar('example', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('example', 'aliasname', $aliasname);
        xarModSetVar('example', 'useModuleAlias', $modulealias);
    } else{
        xarModSetVar('example', 'aliasname', '');
        xarModSetVar('example', 'useModuleAlias', 0);

    }

    $useAliasName = xarModGetVar('example', 'useModuleAlias');
    $aliasname = trim(xarModGetVar('example','aliasname'));
    /* let's set the alias if it's chosen
     * else we want to delete it from the module alias list
     */
    if (($useAliasName==1) && !empty($aliasname)){
        $usealias = 1;
        xarModSetAlias($aliasname,'example');
    } elseif (($useAliasName==0) && !empty($aliasname)) {
        $usealias = 0;
        xarModDelAlias($aliasname,'example');
    } else {
        $usealias=0;
    }
    xarModCallHooks('module','updateconfig','example',
                   array('module' => 'example'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('example', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>