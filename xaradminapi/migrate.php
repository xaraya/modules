<?php
/**
 * Dossier Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Dossier Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
/**
 * Update an example item
 *
 * @author the Example module development team
 * @param  $args ['exid'] the ID of the item
 * @param  $args ['name'] the new name of the item
 * @param  $args ['number'] the new number of the item
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function dossier_adminapi_migrate($args)
{
    extract($args);

    $invalid = array();
    
    if (!isset($component) || !is_string($component)) {
        $invalid[] = 'component';
    }
    
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'DOSSIER');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    
    if(!xarSecurityCheck('AdminDossier')) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    
    if(!xarModAPILoad('xproject','user')) {return;}
    if(!xarModAPILoad('xtasks','user')) {return;}
    
    $dossier_table = $xartable['dossier_contacts'];
    $xProjects_table = $xartable['xProjects'];
    $addressbook_links_table = $xartable['dossier_addressbook_links'];
    $team_table = $xartable['xProject_team'];
    $xtasks_table = $xartable['xtasks'];
    $reminders_table = $xartable['xtasks_reminders'];
    $worklog_table = $xartable['xtasks_worklog'];
    $roles_table = $xartable['roles'];
    
    switch($component) {
        case "projectclient":
            // only perform if ddata field is set to addressbook contactlist
    
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $projects_objectid));
            foreach ($fields as $name => $info) {
                if($name == "clientid") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $xProjects_table a, $addressbook_links_table b
                                    SET a.clientid = b.contactid
                                    WHERE a.clientid = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            $query = "UPDATE $xProjects_table a, $dossier_table b, $roles_table c
                                    SET a.clientid = b.contactid
                                    WHERE a.clientid = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;   
        case "projectagent":
            // only perform if ddata field is set to addressbook contactlist
    
            $projects_objectid = xarModGetVar('xproject','projects_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $projects_objectid));
            foreach ($fields as $name => $info) {
                if($name == "ownerid") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $xProjects_table a, $addressbook_links_table b
                                    SET a.ownerid = b.contactid
                                    WHERE a.ownerid = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $xProjects_table a, $dossier_table b, $roles_table c
                                    SET a.ownerid = b.contactid
                                    WHERE a.ownerid = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;  
        case "projectteam":
            
            // only perform if ddata field is set to addressbook contactlist
    
            $team_objectid = xarModGetVar('xproject','team_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $team_objectid));
            foreach ($fields as $name => $info) {
                if($name == "memberid") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $team_table a, $addressbook_links_table b
                                    SET a.memberid = b.contactid
                                    WHERE a.memberid = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $team_table a, $dossier_table b, $roles_table c
                                    SET a.memberid = b.contactid
                                    WHERE a.memberid = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;  
        case "taskcreator":            
            
            // only perform if ddata field is set to addressbook contactlist
    
            $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $xtasks_objectid));
            foreach ($fields as $name => $info) {
                if($name == "creator") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $xtasks_table a, $addressbook_links_table b
                                    SET a.creator = b.contactid
                                    WHERE a.creator = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $xtasks_table a, $dossier_table b, $roles_table c
                                    SET a.creator = b.contactid
                                    WHERE a.creator = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;  
        case "taskowner":                 
            
            // only perform if ddata field is set to addressbook contactlist
    
            $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $xtasks_objectid));
            foreach ($fields as $name => $info) {
                if($name == "owner") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $xtasks_table a, $addressbook_links_table b
                                    SET a.owner = b.contactid
                                    WHERE a.owner = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}    
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $xtasks_table a, $dossier_table b, $roles_table c
                                    SET a.owner = b.contactid
                                    WHERE a.owner = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;  
        case "taskassigner":                    
            
            // only perform if ddata field is set to addressbook contactlist
    
            $xtasks_objectid = xarModGetVar('xtasks','xtasks_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $xtasks_objectid));
            foreach ($fields as $name => $info) {
                if($name == "assigner") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $xtasks_table a, $addressbook_links_table b
                                    SET a.assigner = b.contactid
                                    WHERE a.assigner = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}     
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $xtasks_table a, $dossier_table b, $roles_table c
                                    SET a.assigner = b.contactid
                                    WHERE a.assigner = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;  
        case "taskreminder":                       
            
            // only perform if ddata field is set to addressbook contactlist
    
            $reminders_objectid = xarModGetVar('xtasks','reminders_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $reminders_objectid));
            foreach ($fields as $name => $info) {
                if($name == "ownerid") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $reminders_table a, $addressbook_links_table b
                                    SET a.memberid = b.contactid
                                    WHERE a.memberid = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}        
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $reminders_table a, $dossier_table b, $roles_table c
                                    SET a.ownerid = b.contactid
                                    WHERE a.ownerid = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                        
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break;  
        case "taskworklog":                        
            
            // only perform if ddata field is set to addressbook contactlist
    
            $worklog_objectid = xarModGetVar('xtasks','worklog_objectid');
            $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                    array('objectid' => $worklog_objectid));
            foreach ($fields as $name => $info) {
                if($name == "ownerid") {
                    $contact_type = $info['type'];
                    switch($contact_type) {
                        case 735:
                            // auto-switch the ddata field to dossier contactlist, if it isn't already
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                        
                            $query = "UPDATE $worklog_table a, $addressbook_links_table b
                                    SET a.ownerid = b.contactid
                                    WHERE a.ownerid = b.addressbook_id";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}       
                            break;
                        case 779:
                            // already assigned to dossier
                            break;
                        case 37: // userlist 
                            
                            if (!xarModAPIFunc('dynamicdata','admin','updateprop',
                                              array('prop_id' => $info['id'],
                                                    'label' => $info['label'],
                                                    'type' => 779,
                                                    'default' => "",
                                                    'status' => $info['status'],
                                                    'validation' => ""))) {
                                return;
                            }
                            
                            $query = "UPDATE $worklog_table a, $dossier_table b, $roles_table c
                                    SET a.ownerid = b.contactid
                                    WHERE a.ownerid = c.xar_uid
                                    AND b.email_1 = c.xar_email";
                            $result = &$dbconn->Execute($query);
                            if (!$result) {return;}
                        
                            break;
                    }
                    // 735 -> addressbook
                    // 779 -> dossier
                }
            }     
            break; 
    
    }

    return true;
}
?>
