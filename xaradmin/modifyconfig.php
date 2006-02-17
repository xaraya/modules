<?php
/**
 * Standard function to modify configuration parameters
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @link http://xaraya.com/index.php/release/
 * @author Michel V.
 */
/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author SIGMAPersonnel module development team
 * @return array
 */
function sigmapersonnel_admin_modifyconfig()
{
    // Initialise the $data variable
    $data = xarModAPIFunc('sigmapersonnel', 'admin', 'menu');

    if (!xarSecurityCheck('AdminSIGMAPersonnel')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();


    //$data['boldchecked'] = xarModGetVar('sigmapersonnel', 'bold') ? true : false;
    // ID of item being on call

    $data['persstatusses'] = xarModAPIFunc('sigmapersonnel', 'user', 'gets',
                                      array('itemtype' => 6));

    $data['OnCallID'] = xarModGetVar('sigmapersonnel', 'OnCallID');
    // Note : if you don't plan on providing encode/decode functions for
    // short URLs (see xaruserapi.php), you should remove these from your
    // admin-modifyconfig.xard template !
    $data['shorturlschecked'] = xarModGetVar('sigmapersonnel', 'SupportShortURLs') ? true : false;
    /* If you plan to use alias names for you module then you should use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('sigmapersonnel', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('sigmapersonnel','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'sigmapersonnel',
        array('module' => 'sigmapersonnel'));
    if (empty($hooks)) {
        $data['hookoutput'] = array();
    } else {
        // You can use the output from individual hooks in your template too, e.g. with
        // $hookoutput['categories'], $hookoutput['dynamicdata'], $hookoutput['keywords'] etc.
        $data['hookoutput'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}

?>
