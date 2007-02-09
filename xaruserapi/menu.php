<?php
/**
* Get user menu vars
*
* @package modules
* @copyright (C) 2002-2007 The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage highlight
* @link http://xaraya.com/index.php/release/559.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
* Get user menu vars
*
* Generate list of "menu" vars (actually, generate vars that are
* needed on every page in the user section)
*
* @author  Curtis Farnham <curtis@farnham.com>
* @access  public
* @return array list of menu parameters
*/
function highlight_userapi_menu()
{
    // initialize menu data
    $menu = array();
    $menu['menutitle'] = xarML('Highlight');

    // retrieve status message (if any) and reset it
    $menu['statusmsg'] = xarSessionGetVar('statusmsg');
    xarSessionSetVar('statusmsg', '');

    return $menu;
}

?>
