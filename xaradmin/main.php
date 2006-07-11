<?php
/**
 * Main function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AuthSQL Module
 * @link http://xaraya.com/index.php/release/10512.html
 * @author Roger Keays and James Cooper
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