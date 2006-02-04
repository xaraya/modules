<?php
/**
 * Main user function
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 */

function julian_user_main()
{
    // Security check
    if (!xarSecurityCheck('ViewJulian')) return;
    // redirect the user to the default view
    xarResponseRedirect(xarModURL('julian','user','month'));
}

?>
