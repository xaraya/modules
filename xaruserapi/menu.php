<?php
/**
 * File: $Id:
 *
 * Generate the common menu configuration
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * generate the common menu configuration
 */
function bible_userapi_menu($args)
{
    extract($args);

    // default values
    if (empty($func)) $func = '';

    // get other vars
    $menutitle = xarML('Bible Search');
    $menulinks = xarModAPIFunc('bible', 'user', 'getmenulinks');

    // initialize menu vars
    $menu = array();

    // set menu vars
    $menu['menutitle'] = $menutitle;
    $menu['func'] = $func;
    $menu['menulinks'] = $menulinks;
    $menu['status'] = '';

    return $menu;
}

?>
