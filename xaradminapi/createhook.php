<?php

function dossier_adminapi_createhook($args)
{
    extract($args);
    
    $modid = '';
    if( !empty($extrainfo['module']) )
    {
        $modid = xarModGetIdFromName($extrainfo['module']);
    }

    $itemtype = '';
    if( !empty($extrainfo['itemtype']) )
    {
        $itemtype = $extrainfo['itemtype'];
    }

    $itemid = '';
    if( !empty($objectid) )
    {
        $itemid = $objectid;
    }
    
    if(isset($extrainfo['realname'])) {
        $realname = $extrainfo['realname'];
    } elseif(isset($extrainfo['name'])) {
        $realname = $extrainfo['name'];
//    } else {
//        $realname = "no name supplied";
    }
    
    if(empty($realname)) return $extrainfo;

    $ownerArgs = array(
        'modid'    => $modid,
        'itemtype' => $itemtype,
        'itemid' => $itemid
    );
    
    // $itemid will not be the uid if module is sitecontact etc
    // ['email'] may not be set
    // roles seems to call this function on role updates, need to fix that

    // NEED TO SET THE PROPER CAT_ID TO USE IN A MODVAR OR SOMETHING
    $contactid = xarModAPIFunc('dossier',
                        'admin',
                        'create',
                        array('cat_id' 	    => 0,
                            'agentuid'	    => 0,
                            'userid'	    => $itemid,
                            'private'	    => 1,
                            'contactcode'	=> "",
                            'prefix'	    => "",
                            'lname'	        => "",
                            'fname'	        => "",
                            'sortname'	    => $realname,
                            'dateofbirth'	=> "",
                            'title'		    => "",
                            'company'	    => "",
                            'sortcompany'	=> "",
                            'img'	        => "",
                            'phone_work'	=> "",
                            'phone_cell'	=> "",
                            'phone_fax'	    => "",
                            'phone_home'	=> "",
                            'email_1'	    => isset($extrainfo['email']) ? $extrainfo['email'] : "",
                            'email_2'	    => "",
                            'chat_AIM'	    => "",
                            'chat_YIM'	    => "",
                            'chat_MSNM'	    => "",
                            'chat_ICQ'	    => "",
                            'contactpref'	=> "email_1",
                            'notes'	        => "",
                            'hooked'        => 1));
    if( !$contactid ) return false;

    return $extrainfo;
}

?>