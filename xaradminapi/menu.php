<?php
/**
* Get admin menu vars
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
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
function files_adminapi_menu()
{
    // initialize menu data
    $menu['menutitle'] = xarML('Files Administration');

    // retrieve status message (if any) and reset it
    $menu['statusmsg'] = xarSessionGetVar('statusmsg');
    xarSessionSetVar('statusmsg', '');

    return $menu;
}

?>
