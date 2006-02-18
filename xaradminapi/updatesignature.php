<?php
/**
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
 * update an owner's signature
 *
 * @author Richard Cave
 * @param $args an array of arguments
 * @param $args['id'] the id of the owner (uid in roles)
 * @param $args['signature'] the signature block of the owner
 * @return bool true on success , or false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_updatesignature($args)
{
    // Get arguments
    extract($args);

    // Argument check
    $invalid = array();

    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'owner ID';
    }

    if (!isset($signature) || !is_string($signature)) {
        $invalid[] = 'signature';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'updatesignature', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // Get item
    $item = xarModAPIFunc('newsletter',
                          'user',
                          'getowner',
                          array('id' => $id));

    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION)
        return; // throw back

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrOwners'];

    // Update the item
    $query = "UPDATE $nwsltrTable
                 SET xar_signature = ?
               WHERE xar_uid = ?";

    $bindvars = array((string) $signature, (int) $id);

    // Execute query
    $result =& $dbconn->Execute($query, $bindvars);

    // Check for an error
    if (!$result) return;

    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'newsletter';
    $item['itemid'] = $id;
    $item['id'] = $id;
    $item['signature'] = $signature;
    xarModCallHooks('item', 'update', $id, $item);

    // Let the calling process know that we have finished successfully
    return true;
}

?>
