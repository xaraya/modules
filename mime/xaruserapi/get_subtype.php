<?php

 /**
  *  Get the name of a mime type 
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  integer    subtypeId   the subtypeID of the mime subtype to lookup (optional)
  *  @param  integer    subtypeName the Name of the mime sub type to lookup (optional)
  *  returns array      An array of (subtypeId, subtypeName) or an empty array
  */
  
function mime_userapi_get_subtype( $args ) {

    extract($args);
    
    if (!isset($subtypeId) && !isset($subtypeName)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime','userapi','get_subtype');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    }
    
    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable     = xarDBGetTables();
    
    $where = ' WHERE ';
    
    if (isset($subtypeId)) {
        $where .= ' xar_mime_subtype_id = ' . $subtypeId;
    } else {
        $where .= " xar_mime_subtype_name = '".strtolower($subtypeName)."'";
    }    
    
    // table and column definitions
    $subtype_table =& $xartable['mime_subtype'];
    
    $sql = "SELECT xar_mime_type_id,
                   xar_mime_subtype_id, 
                   xar_mime_subtype_name
              FROM $subtype_table
            $where";

    $result = $dbconn->Execute($sql);

    if (!$result || $result->EOF)  {
        return array();
    }
    
    $row = $result->GetRowAssoc(false);
    
    return array('typeId'      => $row['xar_mime_type_id'],
                 'subtypeId'   => $row['xar_mime_subtype_id'],
                 'subtypeName' => $row['xar_mime_subtype_name']);
}    
    
?>
