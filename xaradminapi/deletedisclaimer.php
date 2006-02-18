<?php
/*
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
*/


/**
 * delete a disclaimer
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] ID of the disclaimer
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_deletedisclaimer($args)
{
    // Get arguments from argument array
    extract($args);

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getdisclaimer',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrDisclaimers'];

    // Delete the disclaimer
    $query = "DELETE
                FROM $nwsltrTable
               WHERE xar_id = ?";
    $bindvars[] = (int) $id;
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have deleted an item.  As this is a
    // delete hook we're not passing any extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'delete', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
