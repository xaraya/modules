<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
 * @author John Cox
*/

/**
 * Create a new HTML tag
 *
 * @public
 * @author John Cox 
 * @author Richard Cave 
 * @param 'tag' the tag to be created
 * @param 'tagtype' the type of tag to be created
 * @param 'allowed' the state of the tag to be created
 * @raise MISSING_DATA
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
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
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
