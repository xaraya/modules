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
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * @returns bool
 * @return number of jobs run on success, false if not
 * @throws DATABASE_ERROR
 */
function pubsub_adminapi_process_queue($args)
{
    $allindigest = xarModVars::get('pubsub','allindigest');

    if (empty($allindigest)) {
        if (!($count = xarMod::apiFunc('pubsub','admin','process_queue_nodigest',$args) ) ) {
            return;
        }
    } else {
        if (!($count = xarMod::apiFunc('pubsub','admin','process_queue_digest',$args) ) ) {
            return;
        }
    }
    return $count;

}

?>