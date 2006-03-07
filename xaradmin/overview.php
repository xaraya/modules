<?php
/**
 * Overview for xarcachemanager
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarcachemanager
 * @link http://xaraya.com/index.php/release/1652.html
 * @author jsb | mikespub
 */

/**
 * Overview displays standard Overview page
 */
function xarcachemanager_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xarcachemanager', 'admin', 'main', $data, 'main');
}

?>