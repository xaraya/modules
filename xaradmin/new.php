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
 * Add a new tag
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 */
function html_admin_new()
{
    // Security Check
    if(!xarSecurityCheck('AddHTML')) return;
    
    $data['authid'] = xarSecGenAuthKey();
    $data['createbutton'] = xarML('Create Tag');

    // Get tag types
    $types = xarModAPIFunc('html',
                           'user',
                           'getalltypes');

    // Check for exceptions
    if (!isset($types) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    $data['types'] = $types;

    // Include 'formcheck' JavaScript.
    // TODO: move this to a template widget when available.
    xarModAPIfunc(
        'base', 'javascript', 'modulefile',
        array('module'=>'base', 'filename'=>'formcheck.js')
    );

    // Return the output
    return $data;
}

?>
