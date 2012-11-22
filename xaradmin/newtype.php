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
 * Add new html tag
 *
 * @public
 * @author Richard Cave 
 */
function html_admin_newtype()
{
    // Security Check
    if(!xarSecurityCheck('AddHTML')) return;
    
    $data['authid'] = xarSecGenAuthKey();
    $data['createbutton'] = xarML('Create Tag Type');

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
