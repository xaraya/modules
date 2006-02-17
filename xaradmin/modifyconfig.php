<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Surveys Module
 */

/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author Surveys module development team
 * @return array
 */
function surveys_admin_modifyconfig()
{
    $data = array();

    /* common menu configuration */
    $data = xarModAPIFunc('surveys', 'admin', 'menu');

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminSurvey')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    $data['SendEventMailsChecked'] = xarModGetVar('surveys', 'SendEventMails') ? true : false;
    $data['itemsperpage'] = xarModGetVar('surveys', 'itemsperpage');


    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xard template !
     */
    $data['shorturlschecked'] = xarModGetVar('surveys', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('surveys', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('surveys','aliasname');

    /* Take care of hooks like categories */
    $hooks = xarModCallHooks('module', 'modifyconfig', 'surveys',
                       array('module' => 'surveys'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for example module'));
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>