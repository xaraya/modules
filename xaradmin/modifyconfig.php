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

    // Generate a one-time authorisation code for this operation
    $data['authid'] = xarSecGenAuthKey(); 

    // Specify some labels and values for display
    $data['updatebutton'] = xarVarPrepForDisplay(xarML('Update Configuration')); 

    $data['shorturlslabel'] = xarML('Enable short URLs?');
    $data['shorturlschecked'] = xarModGetVar('release', 'SupportShortURLs') ? 'checked' : '';
    $data['itemsvalue'] = xarModGetVar('release', 'itemsperpage');
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