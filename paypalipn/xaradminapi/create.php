<?php
/**
 * create a new item
 * @param $args['args'] Paypal Transaction Post Vars
 * @returns int
 * @return ID on success, false on failure
 */
function paypalipn_adminapi_create($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check - make sure that all required arguments are present,
    // if not then set an appropriate error message and return
    if (!isset($args)) {
        $msg = xarML('Invalid Parameter Count in #(3)_#(1)api_#(2)', 'admin', 'create', '?modname');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    $var_dump = $args['var_dump'];
    //unset($args['var_dump']);

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $table = $xartable['ipnlog'];
    // Get next ID in table
    $nextId = $dbconn->GenId($table);
    // Add item
    $query = "INSERT INTO $table (
              xar_id,
              xar_log)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($var_dump) . "')";
    $result =& $dbconn->Execute($query);
    if (!$result) return;
    // Get the ID of the item that we inserted
    $id = $dbconn->PO_Insert_ID($table, 'xar_id');

    // Let any hooks know that we have created a new link
    // Call create hooks for categories, hitcount etc.
    $args['id'] = $id;
    // Specify the module, itemtype and itemid so that the right hooks are called
    $args['module'] = 'paypalipn';
    $args['itemtype'] = 0;
    $args['itemid'] = $id;
    //$args['extrainfo'] = $var_dump;
    xarModCallHooks('item', 'create', $id, $args);
    // Return the id of the newly created link to the calling process
    return $id;
}
?>