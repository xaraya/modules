<?php
/**
 * Helpdesk Module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Helpdesk Module
 * @link http://www.abraisontechnoloy.com/
 * @author Brian McGilligan <brianmcgilligan@gmail.com>
 */
/**
    Count the numbe of tickets in the DB
*/
function helpdesk_userapi_count_tickets($args)
{
    $args['count'] = true;

    $count = xarModAPIFunc('helpdesk', 'user', 'gettickets', $args);

    return $count;
}
?>