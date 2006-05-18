<?php
/**
 * Subitems module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Subitems Module
 * @link http://xaraya.com/index.php/release/9356.html
 * @author Subitems Module Development Team
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @todo MichelV Make Hooks an array, here and in template
 */
function subitems_admin_modifyconfig()
{
    $data = xarModAPIFunc('subitems', 'admin', 'menu');

    // Security check
    if (!xarSecurityCheck('AdminSubitems')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    // Specify some labels and values for display
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration'));
    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('subitems', 'SupportShortURLs') ? true : false;

    $hooks = xarModCallHooks('module', 'modifyconfig', 'subitems',
        array('module' => 'subitems','itemtype' => 1));

    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('', $hooks);
    } else {
        $data['hooks'] = $hooks;
    }
    // Return the template variables defined in this function
    return $data;
}

?>
