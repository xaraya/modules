<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/732.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function accessmethods_admin_modifyconfig()
{
    // Initialise the $data variable that will hold the data to be used in
    // the blocklayout template, and get the common menu configuration - it
    // helps if all of the module pages have a standard menu at the top to
    // support easy navigation
    $data = xarModAPIFunc('accessmethods','admin','menu');

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AdminAccessMethods')) return;

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey();
    
    $data['webmastergroup'] = xarModGetVar('accessmethods', 'webmastergroup');
    $data['itemsperpage'] = xarModGetVar('accessmethods', 'itemsperpage');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'accessmethods',
                            array('module' => 'accessmethods'));
    if (empty($hooks)) {
        $data['hooks'] = '';
    } elseif (is_array($hooks)) {
        $data['hooks'] = join('',$hooks);
    } else {
        $data['hooks'] = $hooks;
    }

    $data['submitlabel'] = xarML('Submit');
    // Return the template variables defined in this function
    return $data;
}

?>
