<?php
/**
 * Overview for authsso
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage authsso
 * @link http://xaraya.com/index.php/release/51.html
 */

/**
 * Overview displays standard Overview page
 */
function authsso_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('authsso', 'admin', 'main', $data, 'main');
}

?>