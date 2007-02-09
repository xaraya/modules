<?php
/**
* main admin function
*
* @package modules
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* main administration function: redirect to modifyconfig
 * @return bool true on succes of redirect
 */
function highlight_admin_main()
{
    // security check
    if (!xarSecurityCheck('AdminHighlight')) return;

    // Initialise array
    xarResponseRedirect(xarModURL('highlight', 'admin', 'modifyconfig'));
    // success
    return true
}

?>
