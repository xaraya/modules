<?php

function contact_adminapi_update_contact($args)
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
     if (!isset($showzip) || !is_numeric($showzip)) {
         $showzip = 0;
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
                    join(', ',$invalid), 'admin', 'update_contact', 'contact');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

     // Security check - important to do this as early on as possible to
    // avoid potential security holes or just too much wasted processing
    if (!xarSecurityCheck('ContactAdd',0,'Item', "$id:All:All")) return;

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
    $contacttable1 = $xartable['contact_attributes'];

    // Get next ID in table - this is required prior to any insert that
    // uses a unique ID, and ensures that the ID generation is carried
    // out in a database-portable fashion
    $nextId = $dbconn->GenId($contacttable);

    // Add item - the formatting here is not mandatory, but it does make
    // the SQL statement relatively easy to read.  Also, separating out
    // the sql statement from the Execute() command allows for simpler
    // debug operation if it is ever needed
    $query = "UPDATE $contacttable
            SET xar_firstname = '" . xarVarPrepForStore($firstname) . "',
                xar_lastname = '" . xarVarPrepForStore($lastname) . "',
                xar_address = '" . xarVarPrepForStore($address) . "',
                xar_address2 = '" . xarVarPrepForStore($address2) . "',
                xar_city = '" . xarVarPrepForStore($city) . "',
                xar_state = '" . xarVarPrepForStore($state) . "',
                xar_zip = '" . xarVarPrepForStore($zip) . "',
                xar_country = '" . xarVarPrepForStore($country) . "',
                xar_mail = '" . xarVarPrepForStore($mail) . "',
                xar_phone = '" . xarVarPrepForStore($phone) . "',
                xar_fax = '" . xarVarPrepForStore($fax) . "',
                xar_mobile = '" . xarVarPrepForStore($mobile) . "',
                xar_pager = '" . xarVarPrepForStore($pager) . "',
                xar_typephone = '" . xarVarPrepForStore($typephone) . "',
                xar_typefax = '" . xarVarPrepForStore($typefax) . "',
                xar_typemobile = '" . xarVarPrepForStore($typemobile) . "',
                xar_typepager = '" . xarVarPrepForStore($typepager) . "',
                xar_active = '" . xarVarPrepForStore($active) . "',
                xar_ICQ = '" . xarVarPrepForStore($ICQ) . "',
                xar_AIM = '" . xarVarPrepForStore($AIM) . "',
                xar_YIM = '" . xarVarPrepForStore($YIM) . "',
                xar_MSNM = '" . xarVarPrepForStore($MSNM) . "',
                xar_titleID = '" . xarVarPrepForStore($titleID) . "',
                xar_image = '" . xarVarPrepForStore($image) . "',
                xar_hide = '" . xarvarPrepForStore($hide) . "'
            WHERE xar_id = " . xarVarPrepForStore($id);

    $result = $dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

     $query = "UPDATE $contacttable1
            SET xar_showname = '" . xarVarPrepForStore($showname) . "',
                xar_showaddress = '" . xarVarPrepForStore($showaddress) . "',
                xar_showaddress2 = '" . xarVarPrepForStore($showaddress2) . "',
                xar_showcity = '" . xarVarPrepForStore($showcity) . "',
                xar_showstate = '" . xarVarPrepForStore($showstate) . "',
                xar_showzip = '" . xarVarPrepForStore($showzip) . "',
                xar_showcountry = '" . xarVarPrepForStore($showcountry) . "',
                xar_showemail = '" . xarVarPrepForStore($showemail) . "',
                xar_showphone = '" . xarVarPrepForStore($showphone) . "',
                xar_showfax = '" . xarVarPrepForStore($showfax) . "',
                xar_showmobile = '" . xarVarPrepForStore($showmobile) . "',
                xar_showpager = '" . xarVarPrepForStore($showpager) . "',
                xar_showICQ = '" . xarVarPrepForStore($showICQ) . "',
                xar_showAIM = '" . xarVarPrepForStore($showAIM) . "',
                xar_showYIM = '" . xarVarPrepForStore($showYIM) . "',
                xar_showMSNM = '" . xarVarPrepForStore($showMSNM) . "',
                xar_showtitle = '" . xarVarPrepForStore($showtitle) . "',
                xar_showdepartment = '" . xarVarPrepForStore($showdepartment) . "',
                xar_showimage = '" . xarvarPrepForStore($showimage) . "'
            WHERE xar_id = " . xarVarPrepForStore($id);

     $result = $dbconn->Execute($query);
    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;
    // Get the ID of the item that we inserted.  It is possible, depending
    // on your database, that this is different from $nextId as obtained
    // above, so it is better to be safe than sorry in this situation
    $id = $dbconn->PO_Insert_ID($contacttable, 'xar_id');

    // Let any hooks know that we have created a new item.  As this is a
    // create hook we're passing 'exid' as the extra info, which is the
    // argument that all of the other functions use to reference this
    // item

    // Return the id of the newly created item to the calling process
    return $id;
}

?>