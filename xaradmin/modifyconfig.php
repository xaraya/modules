<?php
/**
 * Standard function to modify configuration parameters
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
/**
 * This is a standard function to modify the configuration parameters of the
 * module
 */
function release_admin_modifyconfig()
{
    // Security check
    if (!xarSecurityCheck('AdminRelease')) return; 
    if (!xarVarFetch('phase', 'str:1:100', $phase, 'modify', XARVAR_NOT_REQUIRED, XARVAR_PREP_FOR_DISPLAY)) return;
    if (!xarVarFetch('tab', 'str:1:100', $data['tab'], 'general', XARVAR_NOT_REQUIRED)) return;

    $data['module_settings'] = xarMod::apiFunc('base','admin','getmodulesettings',array('module' => 'release'));
    $data['module_settings']->setFieldList('items_per_page, use_module_alias, module_alias_name, enable_short_urls','use_module_icons, frontend_page, backend_page');
    $data['module_settings']->getItem();

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 

    // Specify some labels and values for display
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration')); 

    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModVars::get('release', 'SupportShortURLs') ? 'checked' : '';
    $data['itemsvalue'] = xarModVars::get('release', 'itemsperpage');
    $data['itemslabel'] = xarML('Release items per page:');
    if (!isset($data['itemsvalue'])) {
        $data['itemsvalue']=20;
    }
    $hooks = xarModCallHooks('module', 'modifyconfig', 'release',
        array('module' => 'release'));
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