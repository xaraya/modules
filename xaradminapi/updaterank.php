<?php
/**
 * Get the score for a user
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Userpoints Module
 * @link http://xaraya.com/index.php/release/782.html
 * @author Userpoints Module Development Team
 */
/**
 * update a rank
 *
 * @author the Userpoints module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_adminapi_updaterank($args)
{

    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($id) || !is_numeric($id)) {
        $invalid[] = 'rank ID';
    }
    if (!isset($rankname) || !is_string($rankname)) {
        $invalid[] = 'rankname';
    }
    if (!isset($rankminscore) || !is_numeric($rankminscore)) {
        $invalid[] = 'rankminscore';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'updaterank', 'Userpoints');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // The user API function is called.  This takes the item ID which
    // we obtained from the input and gets us the information on the
    // appropriate item.  If the item does not exist we post an appropriate
    // message and return
    $item = xarModAPIFunc('userpoints',
                          'user',
                          'getrank',
        array('id' => $id));
    // Check for exceptions
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // Security check
    if (!xarSecurityCheck('EditUserpointsRank', 1, 'Rank', "$item[rankname]:$id")) {
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $ranks = $xartable['userpoints_ranks'];
    // Update the item
    $query = "UPDATE $ranks
            SET xar_rankname = ?,
                xar_rankminscore = ?
            WHERE xar_id = ?";
    $result = &$dbconn->Execute($query, array($rankname, $rankminscore, (int)$id));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Let any hooks know that we have updated an item.  As this is an
    // update hook we're passing the updated $item array as the extra info
    $item['module'] = 'userpoints';
    $item['itemid'] = $id;
    $item['rankname'] = $rankname;
    $item['rankminscore'] = $rankminscore;
    xarModCallHooks('item', 'update', $id, $item);
    // Let the calling process know that we have finished successfully
    return true;
}
?>
