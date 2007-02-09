<?php
/**
* Get admin menu vars
*
* @package unassigned
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage files
* @link http://xaraya.com/index.php/release/554.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get admin menu vars
*
* Generate list of "menu" vars (actually, generate vars that are
* needed on every page in the admin section)
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return  array
* @returns list of menu parameters
*/
function highlight_adminapi_menu()
{
    $menu = array();
    $menu['menutitle'] = xarML('Highlight Administration');

    // get status message and reset it for next use
    $menu['statusmsg'] = xarSessionGetVar('statusmsg');
    xarSessionSetVar('statusmsg', '');

    return $menu;
}

?>
