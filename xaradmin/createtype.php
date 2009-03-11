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
    if (!xarVarFetch('tagtype', 'str:1:', $tagtype, '')) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AddHTML')) return;

    // Check arguments
    if (empty($tagtype)) {
        $msg = xarML('No tag type provided, Please go back and provide a tag type.');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    $id = xarModAPIFunc('html',
                        'admin',
                        'createtype',
                        array('tagtype' => $tagtype));

    if (!$id) {
        return false; //throw back
    }

    xarResponseRedirect(xarModURL('html', 'admin', 'viewtypes'));

    // Return
    return true;
}

?>
