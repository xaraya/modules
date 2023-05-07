<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Set the allowed HTML 
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 */
function html_admin_set()
{
    // Initialise the variable
    $data['items'] = array();

    // Specify some labels for display
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSec::genAuthKey();

    // Security Check
    if(!xarSecurity::check('AdminHTML')) return;

    // The user API function is called.
    $allowed = xarMod::apiFunc('html',
                             'user',
                             'getalltags');

    // Check for exceptions
    if (!isset($allowed) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Add the edit and delete urls
    for ($idx = 0; $idx < count($allowed); $idx++) {
        if (xarSecurity::check('EditHTML')) { 
            $allowed[$idx]['editurl'] = xarController::URL('html',
                                                  'admin',
                                                  'edit',
                                                  array('id' => $allowed[$idx]['id']));
        } else {
            $allowed[$idx]['editurl'] = '';
        }

        if (xarSecurity::check('ManageHTML')) { 
            $allowed[$idx]['deleteurl'] = xarController::URL('html',
                                                    'admin',
                                                    'delete',
                                                    array('id' => $allowed[$idx]['id']));
        } else {
            $allowed[$idx]['deleteurl'] = '';
        }
    }

    // Add the array of items to the template variables
    $data['items'] = $allowed;

    // Return the template variables defined in this function
    return $data;
}

?>s