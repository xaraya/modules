<?php

 /**
  *  Get the typeId and subtypeId for a named mimeType (ie: application/octet-stream)
  *
  *  @author Carl P. Corliss
  *  @access public
  *  @param  string     the mime type we want to lookup id's for 
  *  returns array      An array of (subtypeId, subtypeName) or an empty array
  */
  
function mime_userapi_get_rev_mimetype( $args ) 
{

    extract($args);
    
    if (!isset($mimeType)) {
        $msg = xarML('No (usable) parameter to work with (#(1)::#(2)::#(3))', 'mime','userapi','get_subtype');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return FALSE;
    }
    
    $mimeType = explode('/', $mimeType);

    $typeInfo = xarModAPIFunc('mime','user','get_type', array('typeName' => $mimeType[0]));
    if (!isset($typeInfo['typeId'])) {
        // if not found return 0 for the id of both type / subtype
        return array('typeId' => 0, 'subtypeId' => 0);
    } else {
        $typeId =& $typeInfo['typeId'];
    }
    
    $subtypeInfo = xarModAPIFunc('mime', 'user', 'get_subtype', array('subtypeName' => $mimeType[1]));
    
    if (!isset($subtypeInfo['subtypeId'])) {
        // if not found return 0 for the subtypeId
        return array('typeId' => $typeId, 'subtypeId' => 0);
    } else {
        return array('typeId' => $typeId, 'subtypeId' => $subtypeInfo['subtypeId']);
    }
        
}    
    
?>