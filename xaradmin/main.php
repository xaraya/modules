<?php
/**
 * Headlines - Generates a list of feeds
 *
 * @package modules
 * @copyright (C) 2005-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage headlines module
 * @link http://www.xaraya.com/index.php/release/777.html
 * @author John Cox
 */
/**
 * Main administration function
 *
 * Redirect to view function
 * @return bool true on success of redirect
 */
function headlines_admin_main()
{
    // Security Check
    if(!xarSecurityCheck('EditHeadlines')) return;
    xarResponseRedirect(xarModURL('headlines', 'admin', 'view'));
    // success
    return true;
}
?>
