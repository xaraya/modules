<?php
/**
    Gets the various security levels
    
    @param none
    
    @return array contains the security levels    
*/
function security_userapi_getlevels($args)
{
    $levels = array();
    
    $levels[] = array(
        'name'  => 'overview', 
        'label' => 'Overview', 
        'level' => SECURITY_OVERVIEW
    );
    $levels[] = array(
        'name'  => 'read', 
        'label' => 'Read', 
        'level' => SECURITY_READ
    );
    $levels[] = array(
        'name'  => 'comment', 
        'label' => 'Comment', 
        'level' => SECURITY_COMMENT
    );
    $levels[] = array(
        'name'  => 'write', 
        'label' => 'Write', 
        'level' => SECURITY_WRITE
    );
    $levels[] = array(
        'name'  => 'manage', 
        'label' => 'Manage', 
        'level' => SECURITY_MANAGE
    );
    $levels[] = array(
        'name'  => 'admin', 
        'label' => 'Admin', 
        'level' => SECURITY_ADMIN
    );
    
    return $levels;
}
?>