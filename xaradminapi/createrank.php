<?php
/**
 * Create a new Userpoints rank
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage userpoints
 * @author Userpoints module development team
 */
/**
 * create a new example item
 *
 * @author the Example module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return example item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function userpoints_adminapi_createrank($args)
{
    extract($args);
    // Argument check
    $invalid = array();
    if (!isset($rankname) || !is_string($rankname)) {
        $invalid[] = 'rankname';
    }
    if (!isset($rankminscore) || !is_numeric($rankminscore)) {
        $invalid[] = 'rankminscore';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'Userpoints');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('AddUserpointsRank', 1, 'Rank', "$rankname:All")) {
        return;
    }
    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn()
    // we currently just want the first item, which is the official
    // database handle.  For xarDBGetTables() we want to keep the entire
    // tables array together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you
    // are getting - $table and $column don't cut it in more complex
    // modules
    $ranks = $xartable['userpoints_ranks'];
    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
    $nextId = $dbconn->GenId($ranks);
    $query = "INSERT INTO $ranks (
              xar_id,
              xar_rankname,
              xar_rankminscore)
            VALUES (?,?,?)";
    $result = &$dbconn->Execute($query, array($nextId, $rankname, $rankminscore));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $id = $dbconn->PO_Insert_ID($ranks, 'xar_id');
    // Let any hooks know that we have created a new item.
    $item = $args;
    $item['module'] = 'userpoints';
    $item['itemid'] = $id;
    xarModCallHooks('item', 'create', $id, $item);
    // Return the id of the newly created item to the calling process
    return $id;
}

?>