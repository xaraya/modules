<?php

function contact_adminapi_create_contact($args)
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
    if (!isset($firstname) || !is_string($firstname)) {
        $invalid[] = 'firstname';
    }
    if (!isset($lastname) || !is_string($lastname)) {
        $invalid[] = 'lastname';
    }
    if (!isset($address) || !is_string($address)) {
        $invalid[] = 'address';
    }
     if (!isset($address2) || !is_string($address2)) {
        $invalid[] = 'address2';
    }
    if (!isset($city) || !is_string($city)) {
        $invalid[] = 'city';
    }
      if (!isset($state) || !is_string($state)) {
        $invalid[] = 'state';
    }
     if (!isset($zip) || !is_string($zip)) {
        $invalid[] = 'zip';
    }
     if (!isset($country) || !is_string($country)) {
        $invalid[] = 'country';
    }
     if (!isset($mail) || !is_string($mail)) {
        $invalid[] = 'mail';
    }
    if (!isset($phone) || !is_string($phone)) {
        $invalid[] = 'phone';
    }
    if (!isset($fax) || !is_string($fax)) {
        $invalid[] = 'fax';
    }
    if (!isset($mobile) || !is_string($mobile)) {
        $invalid[] = 'mobile';
    }
    if (!isset($pager) || !is_string($pager)) {
        $invalid[] = 'pager';
    }
    if (!isset($typephone) || !is_numeric($typephone)) {
        $invalid[] = 'typephone';
    }
    if (!isset($typefax) || !is_numeric($typefax)) {
        $invalid[] = 'typefax';
    }
    if (!isset($typemobile) || !is_numeric($typemobile)) {
        $invalid[] = 'typemobile';
    }
    if (!isset($typepager) || !is_numeric($typepager)) {
        $invalid[] = 'typepager';
    }
    if (!isset($active) || !is_numeric($active)) {
          $active = 0;
    }
    if (!isset($ICQ) || !is_string($ICQ)) {
        $invalid[] = 'ICQ';
    }
    if (!isset($AIM) || !is_string($AIM)) {
        $invalid[] = 'AIM';
    }
    if (!isset($YIM) || !is_string($YIM)) {
        $invalid[] = 'YIM';
    }
    if (!isset($MSNM) || !is_string($MSNM)) {
        $invalid[] = 'MSNM';
    }
    if (!isset($titleID) || !is_numeric($titleID)) {
        $invalid[] = 'titleID';
    }


    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create_contact', 'contact');
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
    $contacttable = $xartable['contact_persons'];

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
              xar_firstname,
              xar_lastname,
              xar_address,
              xar_address2,
              xar_city,
              xar_state,
              xar_zip,
              xar_country,
              xar_mail,
              xar_phone,
              xar_fax,
              xar_mobile,
              xar_pager,
              xar_typephone,
              xar_typefax,
              xar_typemobile,
              xar_typepager,
              xar_active,
              xar_ICQ,
              xar_AIM,
              xar_YIM,
              xar_MSNM,
              xar_titleID,
              xar_image,
              xar_hide)
            VALUES (
              $nextId,
              '" . xarVarPrepForStore($firstname) . "',
              '" . xarVarPrepForStore($lastname) . "',
              '" . xarVarPrepForStore($address) . "',
              '" . xarVarPrepForStore($address2) . "',
              '" . xarVarPrepForStore($city) . "',
              '" . xarVarPrepForStore($state) . "',
              '" . xarVarPrepForStore($zip) . "',
              '" . xarVarPrepForStore($country) . "',
              '" . xarVarPrepForStore($mail) . "',
              '" . xarVarPrepForStore($phone) . "',
              '" . xarVarPrepForStore($fax) . "',
              '" . xarVarPrepForStore($mobile) . "',
              '" . xarVarPrepForStore($pager) . "',
              '" . xarVarPrepForStore($typephone) . "',
              '" . xarVarPrepForStore($typefax) . "',
              '" . xarVarPrepForStore($typemobile) . "',
              '" . xarVarPrepForStore($typepager) . "',
              '" . xarVarPrepForStore($active) . "',
              '" . xarVarPrepForStore($ICQ) . "',
              '" . xarVarPrepForStore($AIM) . "',
              '" . xarVarPrepForStore($YIM) . "',
              '" . xarVarPrepForStore($MSNM) . "',
              '" . xarVarPrepForStore($titleID) . "',
              '" . xarVarPrepForStore($image) . "',
              '" . xarVarPrepForStore($hide) . "')";
    $result = $dbconn->Execute($query);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $coid = $dbconn->PO_Insert_ID($contacttable, 'xar_id');

    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'exid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item
// TODO: evaluate
//    xarModCallHooks('item', 'create', $exid, 'exid');
    $item = $args;
    $item['module'] = 'contact';
    $item['itemid'] = $coid;
    xarModCallHooks('item', 'create_contact', $coid, $item);

    // Return the id of the newly created item to the calling process
    return $coid;

}

?>