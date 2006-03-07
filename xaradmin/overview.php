<?php
/**
 * Overview for authurl
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage authurl
 * @link http://xaraya.com/index.php/release/42241.html
 */

/**
 * Overview displays standard Overview page
 */
function authurl_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('authurl', 'admin', 'main', $data, 'main');
}

?>