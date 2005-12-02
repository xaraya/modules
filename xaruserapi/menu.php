<?php
/**
* Generate common menu configuration
*
* @package unassigned
* @copyright (C) 2002-2005 by The Digital Development Foundation
* @license GPL {@link http://www.gnu.org/licenses/gpl.html}
* @link http://www.xaraya.com
*
* @subpackage ebulletin
* @link http://xaraya.com/index.php/release/557.html
* @author Curtis Farnham <curtis@farnham.com>
*/
/**
 * generate the common menu configuration
 */
function ebulletin_userapi_menu()
{
    // get status message
    $statusmsg = xarSessionGetVar('statusmsg');
    xarSessionSetVar('statusmsg', '');

    // initialize menu data
    $menu = array();

    // set menu vars
    $menu['menutitle'] = xarML('eBulletin');
    $menu['statusmsg'] = $statusmsg;

    return $menu;

}

?>
