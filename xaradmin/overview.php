<?php
/**
 * Overview for xlink
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xlink
 * @link http://xaraya.com/index.php/release/186.html
 * @author mikespub <mikespub@xaraya.com>
 */


/**
 * Overview displays standard Overview page
 */
function xlink_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xlink', 'admin', 'main', $data, 'main');
}

?>