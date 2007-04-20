<?php
/**
 * Count all events.
 *
 * @package modules
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage Julian Module
 * @link http://xaraya.com/index.php/release/319.html
 * @author Julian Module Development Team
 */
/**
 * Utility function to count the number of Events in the Calendar.
 *
 * initial template: Roger Raymond
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 * @author MichelV <michelv@xaraya.com>
 * @param $args an array of arguments
 * @param int $args['event_id'] The ID of the Event
 * @param int $args['external'] retrieve events marked external (1=true, 0=false) - ToDo:
 * @return integer number of items
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 * @todo MichelV: Include count of linked items
 */
function julian_userapi_countevents($args)
{
    // We don't want startnum and numitems set
    if (isset($args['startnum'])) unset($args['startnum']);
    if (isset($args['numitems'])) unset($args['numitems']);

    // Indicate to getevents that want to count
    $args['docount'] = true;

    // Hand the counting off to the getevents API.
    $count = xarModAPIFunc('julian', 'user', 'getevents', $args);
    return $count;
}

?>