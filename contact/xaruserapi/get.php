<?php

/**
 * get a specific item
 *
 * @author the Example module development team
 * @param $args['exid'] id of example item to get
 * @returns array
 * @return item array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function contact_userapi_get($args)
{
    // Get arguments from argument array - all arguments to this function
    // should be obtained from the $args array, getting them from other places
    // such as the environment is not allowed, as that makes assumptions that
    // will not hold in future versions of Xaraya
    extract($args);

    // Argument check - make sure that all required arguments are present and
    // in the right format, if not then set an appropriate error message
    // and return
    if (!isset($id) || !is_numeric($id)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    'item ID', 'user', 'get', 'contact');
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
    $contacttable = $xartable['contact_persons'];
    $contacttable2 = $xartable['contact_dept_members'];
    $contacttable3  = $xartable['contact_departments'];
    $contacttable4  = $xartable['contact_titles'];
    $contacttable5 = $xartable['contact_infotype'];
    $contacttable6 = $xartable['contact_attributes'];

    // Get item - the formatting here is not mandatory, but it does make the
    // SQL statement relatively easy to read.  Also, separating out the sql
    // statement from the Execute() command allows for simpler debug operation
    // if it is ever needed
    $query = "SELECT xar_id,
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
                   xar_hide
                   FROM $contacttable
           WHERE xar_id = " . xarVarPrepForStore($id);
    $result =& $dbconn->Execute($query);

    // Check for an error with the database code, adodb has already raised
    // the exception so we just return
    if (!$result) return;

    // Check for no rows found, and if so, close the result set and return an exception
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exists');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }

    // Obtain the item information from the result set
    list($id, $firstname, $lastname, $address, $address2, $city, $state, $zip, $country, $mail, $phone, $fax, $mobile, $pager, $typephone, $typefax, $typemobile, $typepager, $active, $ICQ, $AIM, $YIM, $MSNM, $titleID, $image, $hide) = $result->fields;

     $query = "SELECT xar_depid
            FROM $contacttable2
            WHERE xar_id = $id";

    $resultDept = $dbconn->Execute($query);

     if (!$resultDept) return;

         for (; !$resultDept->EOF; $resultDept->MoveNext() ) {
                 $departmentID = $resultDept->fields[0];

                 $query = "SELECT xar_name
                           FROM $contacttable3
                           WHERE xar_id = $departmentID";

                 $resultName = $dbconn->Execute( $query );
                 if (!$resultName) return;

                     for (; !$resultName->EOF; $resultName->MoveNext() ) {
                         $departmentName = $resultName->fields[0];
                    }
        }
        $query = "SELECT xar_name
                           FROM $contacttable4
                           WHERE xar_id = $titleID";

                 $resultTitle = $dbconn->Execute( $query );
                 if (!$resultTitle) return;

                     for (; !$resultTitle->EOF; $resultTitle->MoveNext() ) {
                         $titleName = $resultTitle->fields[0];
                    }

         $query = "SELECT xar_name
                           FROM $contacttable5
                           WHERE xar_cid = $typephone";

                 $resultTypePhone = $dbconn->Execute( $query );
                 if (!$resultTypePhone) return;

                     for (; !$resultTypePhone->EOF; $resultTypePhone->MoveNext() ) {
                         $TypePhone = $resultTypePhone->fields[0];
                    }
         $query = "SELECT xar_name
                           FROM $contacttable5
                           WHERE xar_cid = $typefax";

                 $resultTypeFax = $dbconn->Execute( $query );
                 if (!$resultTypeFax) return;

                     for (; !$resultTypeFax->EOF; $resultTypeFax->MoveNext() ) {
                         $TypeFax = $resultTypeFax->fields[0];
                    }

         $query = "SELECT xar_name
                           FROM $contacttable5
                           WHERE xar_cid = $typemobile";

                 $resultTypeMobile = $dbconn->Execute( $query );
                 if (!$resultTypeMobile) return;

                     for (; !$resultTypeMobile->EOF; $resultTypeMobile->MoveNext() ) {
                         $TypeMobile = $resultTypeMobile->fields[0];
                    }
          $query = "SELECT xar_name
                           FROM $contacttable5
                           WHERE xar_cid = $typepager";

                 $resultTypePager = $dbconn->Execute( $query );
                 if (!$resultTypePager) return;

                     for (; !$resultTypePager->EOF; $resultTypePager->MoveNext() ) {
                         $TypePager = $resultTypePager->fields[0];
                    }

          $query = "SELECT xar_showname,
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
                           xar_showimage
                           FROM $contacttable6
                           WHERE xar_id = $id";

                 $resultShow = $dbconn->Execute( $query );
                 if (!$resultShow) return;

                     for (; !$resultShow->EOF; $resultShow->MoveNext() ) {
                         $showname = $resultShow->fields[0];
                         $showaddress = $resultShow->fields[1];
                         $showaddress2 = $resultShow->fields[2];
                         $showcity = $resultShow->fields[3];
                         $showstate = $resultShow->fields[4];
                         $showzip = $resultShow->fields[5];
                         $showcountry = $resultShow->fields[6];
                         $showemail = $resultShow->fields[7];
                         $showphone = $resultShow->fields[8];
                         $showfax = $resultShow->fields[9];
                         $showmobile = $resultShow->fields[10];
                         $showpager = $resultShow->fields[11];
                         $showICQ = $resultShow->fields[12];
                         $showAIM = $resultShow->fields[13];
                         $showYIM = $resultShow->fields[14];
                         $showMSNM = $resultShow->fields[15];
                         $showtitle = $resultShow->fields[16];
                         $showdepartment = $resultShow->fields[17];
                         $showimage = $resultShow->fields[18];
                    }
    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
   ;
    // Obtain the item information from the result set
//    list($firstname, $lastname, $address, $address2, $city, $state, $zip, $country, $mail, $phone, $fax, $mobile, $pager, $typephone, $typefax, $typemobile, $typepager, $active, $ICQ, $AIM, $YIM, $MSNM, $titleID, $image, $hide) = $result->fields;

    // All successful database queries produce a result set, and that result
    // set should be closed when it has been finished with
    $result->Close();
     $resultDept->Close();
      $resultName->Close();
       $resultTitle->Close();
        $resultTypePhone->Close();
         $resultTypeFax->Close();
          $resultTypeMobile->Close();
           $resultTypePager->Close();
            $resultShow->Close();
    // Security check - important to do this as early on as possible to avoid
    // potential security holes or just too much wasted processing.  Although
    // this one is a bit late in the function it is as early as we can do it as
    // this is the first time we have the relevant information.
    // For this function, the user must *at least* have READ access to this item
   if (xarSecurityCheck('ContactOverview')){

    // Create the item array
    $edit = array('id' => $id,
                  'firstname' => $firstname,
                  'lastname' => $lastname,
                  'address' => $address,
                  'address2' => $address2,
                  'city' => $city,
                  'state' => $state,
                  'zip' => $zip,
                  'country' => $country,
                  'mail' => $mail,
                  'phone' => $phone,
                  'fax' => $fax,
                  'mobile' => $mobile,
                  'pager' => $pager,
                  'typephone' => $typephone,
                  'typefax' => $typefax,
                  'typemobile' => $typemobile,
                  'typepager' => $typepager,
                  'active' => $active,
                  'ICQ' => $ICQ,
                  'AIM' => $AIM,
                  'YIM' => $YIM,
                  'MSNM' => $MSNM,
                  'titleID' => $titleID,
                  'image' => $image,
                  'hide' => $hide,
                  'depname' => $departmentName,
                  'titleName' => $titleName,
                  'TypePhone' => $TypePhone,
                  'TypeFax' => $TypeFax,
                  'TypeMobile' => $TypeMobile,
                  'TypePager' => $TypePager,
                  'showname' => $showname,
                  'showaddress' => $showaddress,
                  'showaddress2' => $showaddress2,
                  'showcity' => $showcity,
                  'showstate' => $showstate,
                  'showzip' => $showzip,
                  'showcountry' => $showcountry,
                  'showemail' => $showemail,
                  'showphone' => $showphone,
                  'showfax' => $showfax,
                  'showmobile' => $showmobile,
                  'showpager' => $showpager,
                  'showICQ' => $showICQ,
                  'showAIM' => $showAIM,
                  'showYIM' => $showYIM,
                  'showMSNM' => $showMSNM,
                  'showtitle' => $showtitle,
                  'showdepartment' => $showdepartment,
                  'showimage' => $showimage,);

    // Return the item array
    return $edit;

    }
}

?>
