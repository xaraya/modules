<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
/**
    Main admin Security module function

    @return string module funtion output
*/
function security_admin_main($args)
{
    if( !Security::check(SECURITY_ADMIN, 'security') ){ return false; }
    extract($args);

    xarResponseRedirect(xarModURL('security', 'admin', 'overview'));

    return false;
}
?>