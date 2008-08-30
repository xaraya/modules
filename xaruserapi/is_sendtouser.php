<?php

    function messages_userapi_is_sendtouser( $args )
    {
        extract($args);
        if (!isset($sendtouser)) throw new Exception(xarML('No sendto user candidate passed'));

        // Get the groups the current user can send to
        $sendtogroups = xarModAPIFunc('messages','user','get_sendtogroups',$args);
        
        // Now get the parents of the candidate sendto user
        $q = new xarQuery('SELECT');
        $q->addtable($xartable['rolemembers'],'rm');
        $q->addfield('rm.parent_id');
        if(!$q->run()) return;
        $parents =  $q->output();
        
        // Check if a parent is in the sendto groups
        is_sendtouser = false;
        foreach ($parents as $parent) {
            if (in_array($parent['parent_id'], $sendtogroups)) {
                is_sendtouser = true;
                break;
            }
        }        
        return $is_sendtouser;
    }
?>    