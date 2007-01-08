<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage ITSP Module
 * @link http://xaraya.com/index.php/release/572.html
 * @author ITSP Module Development Team
 */

/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author ITSP module development team
 * @return array
 * @todo everything
 */
function itsp_admin_modifyconfig()
{
    $data = array();

    /* common menu configuration */
    $data = xarModAPIFunc('itsp', 'admin', 'menu');

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminITSP')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    $data['OverrideSVchecked'] = xarModGetVar('itsp', 'OverrideSV') ? true : false;
    $data['UseStatusVersionschecked'] = xarModGetVar('itsp', 'UseStatusVersions') ? true : false;
    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xd template.
     */
    $data['shorturlschecked'] = xarModGetVar('itsp', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('itsp', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('itsp','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'itsp',
                       array('module' => 'itsp'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for itsp module'));
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>
