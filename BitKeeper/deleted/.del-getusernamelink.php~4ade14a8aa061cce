<?php
//=========================================================================
// Get username link
//=========================================================================
function helpdesk_userapi_getusernamelink($args)
{
    if(is_array($args)){
        extract($args);
    }else{
        $userid = $args;
    }
    
    if ($userid == 0) {
        return ' ';
    }elseif($userid == 1){
        return 'Anonymous';    
    }else{
        $user = xarModAPIFunc('roles', 'user', 'get', array('uid' => $userid));
    }
    
    return $user['name'];
}
?>
