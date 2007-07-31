<?php
/**
 * Newsletter
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsletter module
 * @author Richard Cave <rcave@xaraya.com>
 */
/**
 * Delete an Newsletter owner
 *
 * @author Richard Cave
 * @param array $args an array of arguments
 * @param int   $args['id'] ID of the owner
 * @return bool true on success, false on failure
 * @throws BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function newsletter_adminapi_deleteowner($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'id';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'adminapi', 'deleteowner', 'Newsletter');
        xarErrorSet(XAR_USER_EXCEPTION, 'MISSING_DATA', new DefaultUserException($msg));
        return;
    }

    // The user API function is called
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

    // Security check
    if(!xarSecurityCheck('AdminNewsletter')) return;

    // Name the table and column definitions
    $nwsltrTable = $xartable['nwsltrOwners'];

    // Delete the item
    $query = "DELETE
                FROM $nwsltrTable
               WHERE xar_uid = ?";
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
