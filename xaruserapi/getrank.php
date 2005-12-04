<?php
/**
 * Get a specific rank
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
 * get a specific rank
 *
 * @author the Userpoints module development team
 * @param id $id id of rank to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function userpoints_userapi_getrank($args)
{
    extract($args);
    // Argument check
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'rank ID', 'user', 'getrank', 'Userpoints');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules
    $ranks = $xartable['userpoints_ranks'];
    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT xar_rankname,
                     xar_rankminscore
              FROM $ranks
              WHERE xar_id = ?";
    $result = &$dbconn->Execute($query, array($id));
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This rank does not exists');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    // Obtain the item information from the result set
    list($rankname, $rankminscore) = $result->fields;
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Security check - important to do this as early on as possible to avoid
    // potential security holes or just too much wasted processing.  Although
    // this one is a bit late in the function it is as early as we can do it as
    // this is the first time we have the relevant information.
    // For this function, the user must *at least* have READ access to this item
    if (!xarSecurityCheck('ReadUserpointsRank', 1, 'Rank', "$rankname:$id")) {
        return;
    }
    // Create the item array
    $item = array('id' => $id,
                  'rankname' => $rankname,
                  'rankminscore' => $rankminscore);
    // Return the item array
    return $item;
}

?>
