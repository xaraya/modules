<?php

 /**
  *  Get all mime types
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    typeName  the Name of the mime type to lookup (optional)
  *  returns array      An array of (typeId, typeName) or an empty array
  */
  
function mime_userapi_add_type( $args ) {

    extract( $args );
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();
    
    if (!isset($typeName) || empty($typeName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].', 
                     'typeName','userapi_add_type','mime');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }
    
    // table and column definitions
    $type_table =& $xartable['mime_type'];
    $typeId = $dbconn->GenID($type_table);
        
    $sql = "INSERT 
              INTO $type_table 
                 ( 
                   xar_mime_type_id, 
                   xar_mime_type_name
                 )
            VALUES
                 (
                   $typeId,
                   '". strtolower(xarVarPrepForStore($typeName)) ."'
                 )";
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($type_table, 'xar_mime_type_id');
    }
}    
    
?>
