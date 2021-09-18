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
 * @author Marc Lutolf <marc@luetolf-carroll.com>
 */
/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * nodigest - that is one email per event
 * @returns bool
 */

function pubsub_schedulerapi_process_queue(array $args=[])
{
    $result = xarMod::apiFunc('pubsub', 'admin', 'process_queue', $args);

    return $result;
}
