<?PHP
/**
 * Xaraya wrapper module for DotProject
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
 * Modify the configuration of xarDPLink
 *
 * this was admin_menu in dplink
 * @author MichelV
 */
function xardplink_admin_modifyconfig() {

    if (!xarSecurityCheck('AdminXardplink')) return;
    $data = array();
    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();
    /* Get the values and check box options */
    $data['use_wrapchecked']   = xarModGetVar('xardplink', 'use_wrap') ? true : false;
    $data['use_windowchecked'] = xarModGetVar('xardplink', 'use_window') ? true : false;
    $data['url']               = xarModGetVar('xardplink', 'url');

    return $data;
}

?>
