<?php
/**
 * Standard function to modify configuration parameters
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Maxercalls module
 * @author maxercalls module development team
 */

/**
 * Modify the module settings
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author MichelV
 */
function maxercalls_admin_modifyconfig()
{
    $data = xarModAPIFunc('maxercalls', 'admin', 'menu');
    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('Adminmaxercalls')) return;
    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['itemslabel'] = xarVarPrepForDisplay(xarML('maxercalls Items Per Page?'));
    $data['itemsvalue'] = xarModGetVar('maxercalls', 'itemsperpage');
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('maxercalls', 'SupportShortURLs') ? 'checked' : '';
    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('maxercalls', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('maxercalls','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'maxercalls',
                       array('module' => 'maxercalls'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for example module'));
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}
?>