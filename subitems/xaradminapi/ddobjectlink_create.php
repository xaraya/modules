<?php

/**
 * create a new subitems item
 *
 * @author the subitems module development team
 * @param  $args ['name'] name of the item
 * @param  $args ['number'] number of the item
 * @returns int
 * @return subitems item ID on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function subitems_adminapi_ddobjectlink_create($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    extract($args);
    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set an appropriate error
    // message and return
    // Note : since we have several arguments we want to check here, we'll
    // report all those that are invalid at the same time...
    $invalid = array();
    if (!isset($objectid) ||!is_numeric($objectid))
        $invalid[] = 'objectid';
    if (empty($module))
        $invalid[] = 'module';
    if (!isset($itemtype) ||!is_numeric($itemtype))
        $invalid[] = 'itemtype';
    if (!isset($template))
    	$invalid[] = 'template';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'create', 'subitems');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
  /*  if (!xarSecurityCheck('AddWars', 1, 'Wars', "$enemy:All")) {
        return;
    }      */
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

    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
//    $nextId = $dbconn->GenId($table);
    // Add item - the formatting here is not mandatory, but it does make
    // the SQL statement relatively easy to read.  Also, separating out
    // the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $query = "INSERT INTO {$xartable['subitems_ddobjects']} (
              xar_objectid,
              xar_module,
              xar_itemtype,
              xar_template)
            VALUES (
            	$objectid,
                '".xarVarPrepForStore($module)."',
                $itemtype,
                '".xarVarPrepForStore($template)."'
				)";
    $result = &$dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
	// Get the ID of the item that we inserted.  It is possible, depending
   // on your database, that this is different from $nextId as obtained
   // above, so it is better to be safe than sorry in this situation


    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'warid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
    // TODO: evaluate
    // xarModCallHooks('item', 'create', $warid, 'warid');
    $item = $args;
    $item['module'] = 'subitems';
    $item['itemid'] = $objectid;
	$item['itemtype'] = 1;
    xarModCallHooks('item', 'create', $objectid, $item);
    // Return the id of the newly created item to the calling process
    return true;
}

?>