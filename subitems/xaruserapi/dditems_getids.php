<?php

/**
 * get a specific item
 *
 * @author the subitems module development team
 * @param  $args ['warid'] id of subitems item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function subitems_userapi_dditems_getids($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other places
    // such as the environment is not allowed, as that makes assumptions that
    // will not hold in future versions of Xaraya
    extract($args);
    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($objectid) || !isset($itemid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'invalid count of params', 'user', 'dditems_getids', 'subitems');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // Get database setup - note that both xarDBGetConn() and xarDBGetTables()
    // return arrays but we handle them differently.  For xarDBGetConn() we
    // currently just want the first item, which is the official database
    // handle.  For xarDBGetTables() we want to keep the entire tables array
    // together for easy reference later on
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    // It's good practice to name the table and column definitions you are
    // getting - $table and $column don't cut it in more complex modules

    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT xar_ddid
            FROM {$xartable['subitems_ddids']}
            WHERE xar_objectid = $objectid AND xar_itemid = $itemid;";
    $result = &$dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    $ids = array();
    // Check for no rows found, and if so, close the result set and return an exception
   for (; !$result->EOF; $result->MoveNext()) {
   		list($ddid) = $result->fields;
        $ids[] = $ddid;
    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
    // Create the item array
    // Return the item array
    return $ids;
}

?>