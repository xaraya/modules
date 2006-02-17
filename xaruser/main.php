<?php
/**
 * Main user function
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Return the month function
 * @return array URL of month function
 */

function julian_user_main()
{
    // Security check
    if (!xarSecurityCheck('ViewJulian')) return;
    // redirect the user to the default view
    xarResponseRedirect(xarModURL('julian','user','month'));
}

?>
