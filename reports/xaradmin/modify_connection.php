<?php
/** 
 * Modify a connection
 */
function reports_admin_modify_connection($args) 
{
	list($conn_id) = xarVarCleanFromInput('conn_id');
	extract($args);

	$conn = xarModAPIFunc('reports','user','connection_get',array('conn_id'=>$conn_id));
    extract($conn);
    $data=array(
                'authid' => xarSecGenAuthKey(),
                'updatelabel' => xarML('Update Connection'),
                'conn_id' => $conn_id,
                'name' => $conn['name'],
                'description' => $conn['description'],
                'type' => $conn['type'],
                'server' => $conn['server'],
                'database' => $conn['database'],
                'user' => $conn['user'],
                'password' => $conn['password']);    
	
	return $data;
}

?>