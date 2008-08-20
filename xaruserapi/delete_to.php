<?php
/**
 * Psspl:Added API function for 
 * Deleting message from Inbox
 * Function set the delete_to field to 1
 * @param unknown_type $args
 * @return result
 */
function messages_userapi_delete_to( $args )
{
    extract($args);

    if (!isset($id)) {
        $msg = xarML('Missing #(1) for #(2) function #(3)() in module #(4)',
                     'id', 'userapi', 'delete_to', 'messages');
        throw new Exception($msg);
    }
       
    $dbconn = xarDB::getConn();
    
    $xartable = xarDB::getTables();
    
    $sql = "UPDATE $xartable[comments]
                SET delete_to = ?
            WHERE id      = ?";
    
    
    $delete_to = 1;

    $bindvars = array($delete_to , $id);

    $result = &$dbconn->Execute($sql,$bindvars);
   
    return $result;
}
?>
