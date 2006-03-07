<?php
/**
 * Overview for authsql
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage authsql
 * @link http://xaraya.com/index.php/release/10512.html
 */

/**
 * Overview displays standard Overview page
 */
function authsql_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('authsql', 'admin', 'main', $data, 'main');
}

?>