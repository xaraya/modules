<?php

function contact_adminapi_create_contact_attributes($args)
{

    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other
    // places such as the environment is not allowed, as that makes
    // assumptions that will not hold in future versions of Xaraya
    extract($args);

    // Argument check - make sure that all required arguments are present
    // and in the right format, if not then set value to 0

    $invalid = array();
    if (!isset($showname) || !is_numeric($showname)) {
        $showname = 0;
    }
    if (!isset($showaddress) || !is_numeric($showaddress)) {
         $showaddress = 0;
    }
     if (!isset($showaddress2) || !is_numeric($showaddress2)) {
        $showaddress2 = 0;
    }
    if (!isset($showcity) || !is_numeric($showcity)) {
        $showcity = 0;
    }
      if (!isset($showstate) || !is_numeric($showstate)) {
         $showstate = 0;
    }
     if (!isset($showpostalcode) || !is_numeric($showpostalcode)) {
         $showpostalcode = 0;
    }
     if (!isset($showcountry) || !is_numeric($showcountry)) {
         $showcountry = 0;
    }
     if (!isset($showemail) || !is_numeric($showemail)) {
         $showemail = 0;
    }
    if (!isset($showphone) || !is_numeric($showphone)) {
         $showphone = 0;
    }
    if (!isset($showfax) || !is_numeric($showfax)) {
         $showfax = 0;
    }
    if (!isset($showmobile) || !is_numeric($showmobile)) {
         $showmobile = 0;
    }
    if (!isset($showpager) || !is_numeric($showpager)) {
         $showpager = 0;
    }
     if (!isset($showICQ) || !is_numeric($showICQ)) {
         $showICQ = 0;
    }
    if (!isset($showAIM) || !is_numeric($showAIM)) {
         $showAIM = 0;
    }
    if (!isset($showYIM) || !is_numeric($showYIM)) {
         $showYIM = 0;
    }
    if (!isset($showMSNM) || !is_numeric($showMSNM)) {
         $showMSNM = 0;
    }
    if (!isset($showtitle) || !is_numeric($showtitle)) {
         $showtitle = 0;
    }
    if (!isset($showdepartment) || !is_numeric($showdepartment)) {
         $showdepartment = 0;
    }
    if (!isset($showimage) || !is_numeric($showimage)) {
         $showimage = 0;
    }


    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create_contact_attributes', 'contact');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

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
    $contacttable = $xartable['contact_attributes'];

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
              xar_contacttype,
              xar_showname,
              xar_showaddress,
              xar_showaddress2,
              xar_showcity,
              xar_showstate,
              xar_showzip,
              xar_showcountry,
              xar_showemail,
              xar_showphone,
              xar_showfax,
              xar_showmobile,
              xar_showpager,
              xar_showICQ,
              xar_showAIM,
              xar_showYIM,
              xar_showMSNM,
              xar_showtitle,
              xar_showdepartment,
              xar_showimage)
            VALUES (
              '" . xarVarPrepForStore($id) . "',
              '" . xarVarPrepForStore($contacttype) . "',
              '" . xarVarPrepForStore($showname) . "',
              '" . xarVarPrepForStore($showaddress) . "',
              '" . xarVarPrepForStore($showaddress2) . "',
              '" . xarVarPrepForStore($showcity) . "',
              '" . xarVarPrepForStore($showstate) . "',
              '" . xarVarPrepForStore($showpostalcode) . "',
              '" . xarVarPrepForStore($showcountry) . "',
              '" . xarVarPrepForStore($showemail) . "',
              '" . xarVarPrepForStore($showphone) . "',
              '" . xarVarPrepForStore($showfax) . "',
              '" . xarVarPrepForStore($showmobile) . "',
              '" . xarVarPrepForStore($showpager) . "',
              '" . xarVarPrepForStore($showICQ) . "',
              '" . xarVarPrepForStore($showAIM) . "',
              '" . xarVarPrepForStore($showYIM) . "',
              '" . xarVarPrepForStore($showMSNM) . "',
              '" . xarVarPrepForStore($showtitle) . "',
              '" . xarVarPrepForStore($showdepartment) . "',
              '" . xarVarPrepForStore($showimage) . "')";

    $result = $dbconn->Execute($query);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $coaid = $dbconn->PO_Insert_ID($contacttable, 'xar_id');

    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'exid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
// TODO: evaluate
//    xarModCallHooks('item', 'create', $exid, 'exid');
    $item = $args;
    $item['module'] = 'contact';
    $item['itemid'] = $coaid;
    xarModCallHooks('item', 'create_contact', $coaid, $item);

    // Return the id of the newly created item to the calling process
    return $coaid;
}

?>