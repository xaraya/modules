<?php

 /**
  *  Get the name of a mime type 
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    $typeId     the type ID of the mime type to grab subtypes for 
  *  returns array      An array of (typeid, subtypeId, subtypeName) or an empty array
  */
  
function mime_userapi_getall_subtypes( $args ) {

    extract($args);
    
    if (isset($typeId)) {
        $typeId = (int) $typeId;
        if (is_int($typeId)) {
            $where = " WHERE xar_mime_type_id = $typeId";
        } else {
            $msg = xarML('Supplied parameter [#(1)] for function [#(2)], is not an integer!', 
                         'typeId','mime_userapi_getall_subtypes');
            xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
            return;
        }
    } else {
        $where = '';
    }
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();
    
    // table and column definitions
    $subtype_table =& $xartable['mime_subtype'];
    
    $sql = "SELECT xar_mime_type_id,
                   xar_mime_subtype_id, 
                   xar_mime_subtype_name 
              FROM $subtype_table
            $where
          ORDER BY xar_mime_type_id,
                   xar_mime_subtype_name ASC ";

    $result = $dbconn->Execute($sql);

    if (!$result | $result->EOF)  {
        return array();
    }
    
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        
        $subtypeInfo[$row['xar_mime_subtype_id']]['subtypeId']     = $row['xar_mime_subtype_id'];
        $subtypeInfo[$row['xar_mime_subtype_id']]['subtypeName']   = $row['xar_mime_subtype_name'];
        
        $result->MoveNext();
    }
    $result->Close();

    return $subtypeInfo;
    
}    
    
?>
