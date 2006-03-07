<?php
/**
 * Overview for xarcpshop
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarcpshop
 * @link http://xaraya.com/index.php/release/199.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Overview displays standard Overview page
 */
function xarcpshop_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xarcpshop', 'admin', 'main', $data, 'main');
}

?>