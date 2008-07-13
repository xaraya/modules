<?php
/**
 * XTask Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XTask Module
 * @link http://xaraya.com/index.php/release/704.html
 * @author St.Ego
 */
function xtasks_userapi_menu()
{
    $menu = array();

    $menu['menutitle'] = xarModGetVar('xtasks','todoheading');

    $menu['menulabel_view'] = xarML('Tasks');
    $menu['menulabel_new'] = xarML('New Task');
    $menu['menulabel_search'] = xarML('Search');
    $menu['menulabel_config'] = xarML('Config');

    return $menu;
}
?>
