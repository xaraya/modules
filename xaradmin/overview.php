<?php
/**
 * Overview for xarpages
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarpages
 * @link http://xaraya.com/index.php/release/160.html
 * @author Jason Judge <mikespub@xaraya.com>
 */

/**
 * Overview displays standard Overview page
 */
function xarpages_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xarpages', 'admin', 'main', $data, 'main');
}

?>