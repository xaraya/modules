<?php
/**
 * Overview for xarcachemanager
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage xarCacheManager module
 * @link http://xaraya.com/index.php/release/1652.html
 */
/**
 * Overview displays standard Overview page
 * @return string
 */
function xarcachemanager_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('xarcachemanager', 'admin', 'main', $data, 'main');
}
