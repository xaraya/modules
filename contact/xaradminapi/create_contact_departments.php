<?php

function contact_adminapi_create_contact_departments($args)
{

    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set value to 0


    // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ContactAdd',0,'item', "$id:All:All")) return;

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
    $contacttable = $xartable['contact_dept_members'];

    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
    $nextId = $dbconn->GenId($contacttable);

    // Add item - the formatting here is not mandatory, but it does make
    // the SQL statement relatively easy to read.  Also, separating out
    // the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $query = "INSERT INTO $contacttable (
              xar_id,
              xar_depid)
            VALUES (
              '" . xarVarPrepForStore($id) . "',
              '" . xarVarPrepForStore($department) . "')";

    $result = $dbconn->Execute($query);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $codid = $dbconn->PO_Insert_ID($contacttable, 'xar_id');

    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'exid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
// TODO: evaluate
//    xarModCallHooks('item', 'create', $exid, 'exid');
    $item = $args;
    $item['module'] = 'contact';
    $item['itemid'] = $codid;
    xarModCallHooks('item', 'create_contact', $codid, $item);

    // Return the id of the newly created item to the calling process
    return $codid;
}

?>
