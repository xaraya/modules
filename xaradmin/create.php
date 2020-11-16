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
 * Create a new HTML tag
 *
 * @public
 * @author John Cox
 * @author Richard Cave
 * @param string 'tag' the tag to be created
 * @param strng 'tagtype' the type of tag to be created
 * @param int 'allowed' the state of the tag to be created
 * @throws MISSING_DATA
 */
function html_admin_create($args)
{
    // Get parameters from input
    if (!xarVar::fetch('tag', 'str:1:', $tag, '')) {
        return;
    }
    if (!xarVar::fetch('tagtype', 'str:1:', $tagtype, '')) {
        return;
    }
    if (!xarVar::fetch('allowed', 'int:0:', $allowed, 0)) {
        return;
    }

    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) {
        return;
    }

    // Security Check
    if (!xarSecurity::check('AddHTML')) {
        return;
    }

    // Check arguments
    if (empty($tag)) {
        $msg = xarML('No tag Provided, Please go back and provide a tag');
        throw new BadParameterException(null, $msg);
    }

    // The API function is called
    $id = xarMod::apiFunc(
        'html',
        'admin',
        'create',
        array('tag' => $tag,
                               'type' => $tagtype,
                               'allowed' => $allowed)
    );

    xarController::redirect(xarController::URL('html', 'admin', 'set'));

    // Return
    return true;
}
