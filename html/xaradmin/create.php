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
 * @purifiedby Richard Cave 
 * @param 'tag' the tag of the link to be created
 * @param 'title' the title of the link to be created
 * @param 'url' the url of the link to be created
 * @param 'comment' the comment of the link to be created
 * @raise MISSING_DATA
 */
function html_admin_create($args)
{
    // Get parameters from whatever input we need
    $tag = xarVarCleanFromInput('tag');

    extract($args);

    // Confirm authorisation code.
    if (!xarSecConfirmAuthKey()) return;

    // Security Check
	if(!xarSecurityCheck('AddHTML')) return;

    // Check arguments
    if (empty($tag)) {
        $msg = xarML('No tag Provided, Please Go Back and provide an HTML tag');
        xarExceptionSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // The API function is called
    $cid = xarModAPIFunc('html',
                        'admin',
                        'create',
                        array('tag' => $tag));

    xarResponseRedirect(xarModURL('html', 'admin', 'set'));

    // Return
    return true;
}

?>
