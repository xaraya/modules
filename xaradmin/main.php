<?php
/**
* Main administration function
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * Main administration function
 *
 * Redirect to modifyconfig
 * @return bool true on success of redirect
 */
function files_admin_main()
{
    // security check
    if (!xarSecurityCheck('AdminFiles')) return;

    // show overview or redirect to a more useful function
    xarResponseRedirect(xarModURL('files', 'admin', 'modifyconfig'));
    // success
    return true;
}

?>
