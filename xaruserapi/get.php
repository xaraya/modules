<?php
/**
 * Polls Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage polls
 * @author Jim McDonalds, dracos, mikespub et al.
 * @todo We could pass the bulk of this to getall() and expand the status options for getall().
 */

/**
 * get a specific poll
 * @param $args['pid'] id of poll to get (optional)
 * @returns array
 * @return item array, or false on failure
 */
function polls_userapi_get($args)
{
    // Restrict to a single poll.
    $args['fetchone'] = true;

    // Always fetch the options.
    $args['getoptions'] = true;

    // If not fetching on the PID, then get the latest open poll.
    // TODO: we need to change this, to make it more explicit.
    if (empty($args['pid']) && !isset($status)) {
        $args['status'] = 1;
        $args['modid'] = xarModGetIDFromName('polls');
    }

    $items = xarModAPIfunc('polls', 'user', 'getall', $args);

    if (!empty($items)) {
        $item = reset($items);
    } else {
        $item = array();
    }

    return $item;
}

?>