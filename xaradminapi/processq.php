<?php
/**
 * File: $Id$
 *
 * Pubsub Admin API
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Pubsub Module
 * @author Chris Dudley <miko@xaraya.com>
 * @author Garrett Hunter <garrett@blacktower.com>
 */

/**
 * Process the queue and run all pending jobs (executed by the scheduler module)
 * @returns bool
 * @return number of jobs run on success, false if not
 * @raise DATABASE_ERROR
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
