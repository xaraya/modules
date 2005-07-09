<?php
/**
 * File: $Id: main.php,v 1.1.1.1 2003/11/20 05:35:21 roger Exp $
 *
 * AuthSQL Administrative Display Functions
 * 
 * @copyright (C) 2003 ninthave
 * @author James Cooper jbt_cooper@bigpond.com
*/

/**
 * the main administration function
 */
function authsql_admin_main()
{
    // Security check
    if(!xarSecurityCheck('AdminAuthSQL')) return;

    // return array from admin-main template
    return array();
}

?>
