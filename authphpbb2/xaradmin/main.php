<?php
/**
 * File: $Id$
 *
 * AuthphpBB2 Administrative Display Functions
 * 
 */

/**
 * the main administration function
 */
function authphpbb2_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthphpBB2')) return;

    // return array from admin-main template
    return array();
}

?>