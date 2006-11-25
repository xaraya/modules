<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
    if (!xarVarFetch('tag', 'str:1:', $tag, '')) return;
    if (!xarVarFetch('tagtype', 'str:1:', $tagtype, '')) return;
    if (!xarVarFetch('allowed', 'int:0:', $allowed, 0)) return;

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
    if(!xarSecurityCheck('AddHTML')) return;

    // Check arguments
    if (empty($tag)) {
        $msg = xarML('No tag Provided, Please go back and provide a tag');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    $cid = xarModAPIFunc('html',
                         'admin',
                         'create',
                         array('tag' => $tag,
                               'type' => $tagtype,
                               'allowed' => $allowed));

    xarResponseRedirect(xarModURL('html', 'admin', 'set'));

    // Return
    return true;
}

?>
