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
function ebulletin_userapi_menu($args)
{
    extract($args);

    // get status message
    $statusmsg = xarSessionGetVar('statusmsg');
    xarSessionSetVar('statusmsg', '');

    if (empty($tab)) $tab = 'subscriptions';

    // initialize menu data
    $menu = array();

    // set menu vars
    $menu['menulinks'] = xarModAPIFunc('ebulletin', 'user', 'getmenulinks');
    $menu['menutitle'] = xarML('eBulletin');
    $menu['statusmsg'] = $statusmsg;
    $menu['tab']     = $tab;

    return $menu;

}

?>
