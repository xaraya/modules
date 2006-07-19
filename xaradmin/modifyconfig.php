<?php
/**
 * Modify module's configuration
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Lists Module
 * @link http://xaraya.com/index.php/release/46.html
 * @author Jason Judge
 */
/**
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module. There isn't much in lists to configure at the moment.
 *
 * @author Lists module development team
 * @return array
 * @todo MichelV: What is needed here?
 */
function lists_admin_modifyconfig()
{
    $data = array();

    /* common menu configuration if it exists*/
    //$data = xarModAPIFunc('lists', 'admin', 'menu');

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    //if (!xarSecurityCheck('AdminLists')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    //$data['boldchecked'] = xarModGetVar('lists', 'bold') ? true : false;
    $data['itemsvalue'] = xarModGetVar('lists', 'itemsperpage');
    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xard template !
     */
    // $data['shorturlschecked'] = xarModGetVar('lists', 'SupportShortURLs') ? true : false;

    $hooks = xarModCallHooks('module', 'modifyconfig', 'lists',
                       array('module' => 'lists'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for Lists module'));
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
