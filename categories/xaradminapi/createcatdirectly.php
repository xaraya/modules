<?php

function categories_adminapi_createcatdirectly($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if ((!isset($name))        ||
        (!isset($description)) ||
        (!isset($point_of_insertion)))
    {
        $msg = xarML('Invalid Parameter Count', join(', ', $invalid), 'admin', 'create', 'categories');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (!isset($image)) {
        $image = '';
    }
    if (!isset($parent)) {
        $parent = 0;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categoriestable = $xartable['categories'];

    // Get next ID in table
    $nextId = $dbconn->GenId($categoriestable);

    /* Opening space for the new node */
    $SQLquery[1] = "UPDATE $categoriestable
                    SET xar_right = xar_right + 2
                    WHERE xar_right >= "
                    .xarVarPrepForStore($point_of_insertion);

    $SQLquery[2] = "UPDATE $categoriestable
                    SET xar_left = xar_left + 2
                    WHERE xar_left >= "
                    .xarVarPrepForStore($point_of_insertion);
    // Both can be transformed into just one SQL-statement, but i dont know if every database is SQL-92 compliant(?)

    $nextID = $dbconn->GenId($categoriestable);

    $SQLquery[3] = "INSERT INTO $categoriestable (
                                xar_cid,
                                xar_name,
                                xar_description,
                                xar_image,
                                xar_parent,
                                xar_left,
                                xar_right)
                         VALUES ("
                                 .$nextID.","
                                 ."'".xarVarPrepForStore($name)."',"
                                 ."'".xarVarPrepForStore($description)."',"
                                 ."'".xarVarPrepForStore($image)."',"
                                 ."'".xarVarPrepForStore($parent)."',"
                                 .xarVarPrepForStore($point_of_insertion).","
                                 .xarVarPrepForStore($point_of_insertion+1).")";

    for ($i=1;$i<4;$i++)
    {
        $result = $dbconn->Execute($SQLquery[$i]);
        if (!$result) return;
    }


    // Call create hooks for categories, hitcount etc.
    $cid = $dbconn->PO_Insert_ID($categoriestable, 'xar_cid');
    
    //Hopefully Hooks will work-out better these args in the near future
    $args['module'] = 'categories';
    $args['itemtype'] = 0;
    $args['itemid'] = $cid;
    xarModCallHooks('item', 'create', $cid, $args);

    // Get cid to return
    return $cid;
}

?>
