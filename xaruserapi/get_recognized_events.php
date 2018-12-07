<?php
/**
 * Pubsub Module
 *
 * @package modules
 * @subpackage pubsub module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Get an array of the recognized events
 *
 * @returns array
 * @return array of events
*/
function pubsub_userapi_get_recognized_events()
{
    $events = array('all' => xarML('All'));die("X");
    $recognized_events = trim(explode(',', xarModVars::get('pubsub', 'recognized_events')));
    foreach ($recognized_events as $event) $events[$event] = $event;
    return $events;
}

?>
