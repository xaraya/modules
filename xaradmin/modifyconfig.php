<?PHP
/**
 * Standard Utility function pass individual menu items to the main menu
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarDPLink Module
 * @link http://xaraya.com/index.php/release/591.html
 * @author xarDPLink Module Development Team
 */
/**
 * Main administration menu
 * was admin_menu
 */
function xardplink_admin_modifyconfig() {

    if (!xarSecurityCheck('AdminXardplink')) return;
    $data = array();
    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    /* Specify some values for display */
    $data['use_wrapchecked'] = xarModGetVar('xardplink', 'use_wrap') ? true : false;
    $data['use_windowchecked'] = xarModGetVar('xardplink', 'use_window') ? true : false;
    $data['url'] = xarModGetVar('xardplink', 'url');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'xardplink',
                       array('module' => 'xardplink'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for example module'));
    } else {
        $data['hooks'] = $hooks;

         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    return $data;
}

?>
