<?php
/**
 * Overview for authphpbb2
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage authphpbb2
 * @link http://xaraya.com/index.php/release/77102.html
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