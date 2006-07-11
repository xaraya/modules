<?php
/**
 * Main function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 *
 * @subpackage authldap
 * @link http://xaraya.com/index.php/release/77102.html
 * @author Alexander GQ Gerasiov <gq@gq.pp.ru>
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