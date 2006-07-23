<?php
/**
 * Pubsub module
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Pubsub Module
 * @link http://xaraya.com/index.php/release/181.html
 * @author Pubsub Module Development Team
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */
/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * @returns bool
 * @return number of jobs run on success, false if not
 * @throws DATABASE_ERROR
 */
function pubsub_adminapi_processq($args)
{
    if (!($allindigest = xarModGetVar('pubsub','allindigest'))) {
        $allindigest = 0;
    }

    if ($allindigest == 0) {
        if (!($count = xarModAPIFunc('pubsub','admin','processqnodigest',$args) ) ) {
            return;
        } else {
            return $count;
        }
    } else {
        if (!($count = xarModAPIFunc('pubsub','admin','processqdigest',$args) ) ) {
            return;
        } else {
            return $count;
        }
    }
    return $count;

} // END processq

?>
