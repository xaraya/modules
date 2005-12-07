<?php
/**
 * XProject Module - A simple project management module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage XProject Module
 * @link http://xaraya.com/index.php/release/665.html
 * @author XProject Module Development Team
 */
function xproject_userapi_menu()
{
    $menu = array();

    $dateformatlist = array('Please choose a Date/Time Format',
                            '%m/%d/%Y',
                            '%B %d, %Y',
                            '%a, %B %d, %Y',
                            '%A, %B %d, %Y',
                            '%m/%d/%Y %H:%M',
                            '%B %d, %Y %H:%M',
                            '%a, %B %d, %Y %H:%M',
                            '%A, %B %d, %Y %H:%M',
                            '%m/%d/%Y %I:%M %p',
                            '%B %d, %Y %I:%M %p',
                            '%a, %B %d, %Y %I:%M %p',
                            '%A, %B %d, %Y %I:%M %p');
    $menu['dateformatlist'] = $dateformatlist;

    $menu['menutitle'] = xarModGetVar('xproject','todoheading');

    $menu['menulabel_view'] = xarML('Projects');
    $menu['menulabel_new'] = xarML('New Project');
    $menu['menulabel_search'] = xarML('Search');
    $menu['menulabel_config'] = xarML('Config');

    return $menu;
}
?>
