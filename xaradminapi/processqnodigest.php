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
 * nodigest - that is one email per event
 * @returns bool
 * @return number of jobs run on success, false if not
 * @raise DATABASE_ERROR
 */
function pubsub_adminapi_processqnodigest($args)
{
    // Get arguments from argument array
    extract($args);

    // Database information
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $pubsubprocesstable = $xartable['pubsub_process'];

    // Get all jobs in pending state
    $query = "SELECT xar_handlingid,
                     xar_pubsubid,
                     xar_objectid,
                     xar_templateid
              FROM $pubsubprocesstable
              WHERE xar_status = 'pending'";
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // set count to 1 so that the scheduler knows we're doing OK :)
    $count = 1;

    while (!$result->EOF) {
        list($handlingid,$pubsubid,$objectid,$templateid) = $result->fields;
        // run the job passing it the handling, pubsub and object ids.
        xarModAPIFunc('pubsub','admin','runjob',
                      array('handlingid' => $handlingid,
                            'pubsubid' => $pubsubid,
                            'objectid' => $objectid,
                            'templateid' => $templateid));
        $count++;
        $result->MoveNext();
    }
    return $count;

} // END processq

?>
