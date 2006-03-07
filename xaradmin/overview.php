<?php
/**
 * Overview for Window
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Window
 * @link http://xaraya.com/index.php/release/3002.html
 */

/**
 * Overview displays standard Overview page
 */
function window_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('window', 'admin', 'main', $data, 'main');
}

?>