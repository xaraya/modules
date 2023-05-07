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
 * @author Richard Cave
 * @param 'tagtype' the type of tag to be created
 * @throws MISSING_DATA
 */
function html_admin_createtype($args)
{
    // Get parameters from input
    if (!xarVar::fetch('tagtype', 'str:1:', $tagtype, '')) return;

    // Confirm authorisation code.
    if (!xarSec::confirmAuthKey()) return;

    // Security Check
    if(!xarSecurity::check('AddHTML')) return;

    // Check arguments
    if (empty($tagtype)) {
        $msg = xarML('No tag type provided, Please go back and provide a tag type.');
        throw new BadParameterException(null,$msg);
    }

    // The API function is called
    $id = xarMod::apiFunc('html',
                        'admin',
                        'createtype',
                        array('tagtype' => $tagtype));

    if (!$id) {
        return false; //throw back
    }

    xarController::redirect(xarController::URL('html', 'admin', 'viewtypes'));

    // Return
    return true;
}

?>
