<?php
/**
 * Show form to define a new connection
 */
function reports_admin_new_connection() 
{
    $data=array(
                'authid' => xarSecGenAuthKey(),
                'conn_id' => 0,
                'createlabel' => xarML('Create Connection'),
                'name' => xarML('(untitled connection)'),
                'description' => xarML('no description'),
                'type' => 'mysql',
                'server' => 'localhost',
                'database' => 'dbname',
                'user' => 'username',
                'password' => '');
	return $data;
}

?>