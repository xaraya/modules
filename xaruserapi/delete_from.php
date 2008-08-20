<?php
/**
 * Psspl:Added API function for 
 * Deleting message from sent and drafts
 * Function set the delete_to field to 1
 * @param unknown_type $args
 * @return result
 */
function messages_userapi_delete_from( $args )
{
    extract($args);

    if (!isset($id)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'id', 'userapi', 'delete_from', 'messages');
        throw new Exception($msg);
    }
       
    $dbconn = xarDB::getConn();
    
    $xartable = xarDB::getTables();
    
    $sql = "UPDATE $xartable[comments]
                SET delete_from = ?
            WHERE id      = ?";
    
    $delete_from = 1;
    
    $bindvars = array($delete_from , $id);

    $result = &$dbconn->Execute($sql,$bindvars);
    
    return $result;
}   
?>