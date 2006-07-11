<?php
/**
 * Overview for authphpbb2
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
 * Overview displays standard Overview page
 */
function authphpbb2_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('authphpbb2', 'admin', 'main', $data, 'main');
}

?>