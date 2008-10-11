<?php

sys::import('modules.messages.xarincludes.defines');

    function messages_userapi_get_sendtogroups( $args )
    {
        extract($args);
        if (!isset($currentuser)) $currentuser = xarUserGetVar('id');
    
        // First we get all the parents of the current user
        sys::import('modules.query.class.query');
        $xartable = xarDB::getTables();
        $q = new Query('SELECT');
        $q->addtable($xartable['roles'], 'r');
        $q->addtable($xartable['rolemembers'],'rm');
        $q->join('r.id', 'rm.role_id');

        $q->addfield('rm.parent_id');
        $q->eq('id', $currentuser);

        if(!$q->run()) return;
        $parents =  $q->output();

        // Find the groups these parents can send to
        $sendtogroups = array();  
        foreach ($parents as $parent) {
            $allowedgroups = unserialize(xarModItemVars::get('messages',"allowedSendMessages",$parent['parent_id']));
            foreach ($allowedgroups as $allowedgroup) $sendtogroups[$allowedgroup] = $allowedgroup;
        }                
        return $sendtogroups;
    }
?>    
