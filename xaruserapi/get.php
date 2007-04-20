<?php
/**
 * Get an event.
 *
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Julian Module
 * @link http://www.metrostat.net
 * initial template: Roger Raymond
 */
/**
 * Get a single event
 *
 * Get a single event from the events table
 * Later we will look in the linked events table
 *
 * @author Julian package development team
 * @author  MichelV (Michelv@xarayahosting.nl)
 * @access  public
 * @param   int $event_id ID of the event to get
 * @return  array $item
 * @throws  BAD_PARAM list of exception identifiers which can be thrown
 * @todo    Michel V. <#> Implement in Julian.
 */

function julian_userapi_get($args)
{
    // Fetch the items. We are hoping for just one.
    // Put on a limit of two, to save a few system resources.
    $args['numitems'] = 2;
    $items = xarModAPIfunc('julian', 'user', 'getevents', $args);

    // No matching items found.
    if (empty($items)) return;

    // Take the first one.
    // TODO: raise an error if more than one event matched.
    $item = reset($items);

    $item['color'] = xarModAPIFunc('julian', 'user', 'getcolor', array('category' => $item['categories']));
    return $item;
}

?>