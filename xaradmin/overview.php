<?php
/**
 * Security - Provides unix style privileges to xaraya items.
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Security Module
 * @author Brian McGilligan <brian@mcgilligan.us>
 */
function security_admin_overview($args)
{
    if( !xarSecurityCheck('AdminSecurity') ){ return false; }
    extract($args);

    $data = array();

    return $data;
}
?>