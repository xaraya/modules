<?php

 /**
  *  Get the name of a mime type 
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    $typeId      the type ID of the mime type to attch subtypes to
  *  @param  string     $subtypeName the name of the subtype to add
  *  returns array      false on error, the sub type id otherwise
  */
  
function mime_userapi_add_subtype( $args ) {

    extract($args);
    
    if (!isset($typeId)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].', 
                     'typeId','userapi_add_subtypes','mime');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }
    
    if (!isset($subtypeName)) {
        $msg = xarML('Missing parameter [#(1)] for function [#(2)] in module [#(3)].', 
                     'subtypeName','userapi_add_subtype','mime');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();
    
    // table and column definitions
    $subtype_table =& $xartable['mime_subtype'];
    $subtypeId     = $dbconn->GenID($subtype_table);
    
    $sql = "INSERT
              INTO $subtype_table
                 ( 
                   xar_mime_type_id,
                   xar_mime_subtype_id,
                   xar_mime_subtype_name
                 )
            VALUES
                 (
                   $typeId,
                   $subtypeId,
                   '".strtolower(xarVarPrepForStore($subtypeName))."'
                 )";
    
    $result = $dbconn->Execute($sql);

    if (!$result) {
        return FALSE;
    } else {
        return $dbconn->PO_Insert_ID($subtype_table, 'xar_mime_subtype_id');
    }
}    
    
?>
