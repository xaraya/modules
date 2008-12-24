<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Set the tagtypes HTML 
 *
 * @public
 * @author Richard Cave 
 */
function html_admin_viewtypes()
{
    // Initialise the variable
    $data['items'] = array();

    // Specify some labels for display
    $data['submitlabel'] = xarML('Submit');
    $data['authid'] = xarSecGenAuthKey();

    // Security Check
    if(!xarSecurityCheck('AdminHTML')) return;

    // The user API function is called.
    $tagtypes = xarModAPIFunc('html',
                              'user',
                              'getalltypes');

    // Check for exceptions
    if (!isset($tagtypes) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Add the edit and delete urls
    for ($idx = 0; $idx < count($tagtypes); $idx++) {
        if (xarSecurityCheck('EditHTML')) { 
            $tagtypes[$idx]['editurl'] = xarModURL('html',
                                                   'admin',
                                                   'edittype',
                                                   array('id' => $tagtypes[$idx]['id']));
        } else {
            $tagtypes[$idx]['editurl'] = '';
        }

        if (xarSecurityCheck('DeleteHTML')) { 
            $tagtypes[$idx]['deleteurl'] = xarModURL('html',
                                                     'admin',
                                                     'deletetype',
                                                     array('id' => $tagtypes[$idx]['id']));
        } else {
            $tagtypes[$idx]['deleteurl'] = '';
        }
    }

    // Add the array of items to the template variables
    $data['items'] = $tagtypes;

    // Return the template variables defined in this function
    return $data;
}

?>
