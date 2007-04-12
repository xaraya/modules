<?php
/**
 * Access Methods Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Access Methods Module
 * @link http://xaraya.com/index.php/release/333.html
 * @author St.Ego <webmaster@ivory-tower.net>
 */
 
/**
 * view items
 */
function xproject_admin_migrate()
{
    if (!xarVarFetch('action', 'str::', $action, '', XARVAR_NOT_REQUIRED)) return;
            
    if (!xarSecurityCheck('AdminDossier', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    // BEGIN ADDRESSBOOK -> DOSSIER TRANSLATION FOR XPROJECT/XTASKS
    // xproject clientid
    // xproject team list
    // xtasks owner
        $projects_objectid = xarModGetVar('xproject', 'projects_objectid');
        $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                array('objectid' => $projects_objectid));
        if($fields) {
            foreach ($fields as $name => $info) {
                if($name == "clientid") {
                    if($info['type'] == 37) {
                        // userlist - no change
                    } elseif($into['type'] == 779) {
                        // dossier contactlist - already in use, no action
                    } elseif($into['type'] == 735) {
                        // addressbook contactlist - translate
                        die("check");
                        $projectlist = xarModAPIFunc('xproject','user','getall');
                        foreach($projectlist as $projectinfo) {
                            if(isset($newcontacts[$projectinfo['clientid']])) {
                                $projectinfo['clientid'] = $newcontacts[$projectinfo['clientid']]
                                if(!xarModAPIFunc('xproject','admin','update',$projectinfo)) { return; }
                            }
                        }
                    }                    
//                    echo "owner info: <pre>"; print_r($info); die("</pre>");
                }
            }
        }
        $team_objectid = xarModGetVar('xproject', 'team_objectid');
        $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                array('objectid' => $team_objectid));
        if($fields) {
            foreach ($fields as $name => $info) {
                if($name == "memberid") {
                    if($info['type'] == 37) {
                        // userlist - no change
                    } elseif($into['type'] == 779) {
                        // dossier contactlist - already in use, no action
                    } elseif($into['type'] == 735) {
                        // addressbook contactlist - translate
                        die("check");
                        if(!xarModAPIFunc('dossier','admin','migrate',
                                        array('component' => "projectteam",
                                            'addressbookid' => $contactinfo['id'],
                                            'contactid' => $contactid))) {
                            return;
                        }
                    }                    
//                    echo "owner info: <pre>"; print_r($info); die("</pre>");
                }
            }
        }
        
        
        
        $xtasks_objectid = xarModGetVar('xtasks', 'xtasks_objectid');
        $fields = xarModAPIFunc('dynamicdata','user','getprop',
                                array('objectid' => $xtasks_objectid));
        if($fields) {
            foreach ($fields as $name => $info) {
                if($name == "owner") {
                    if($info['type'] == 37) {
                        // userlist - no change
                    } elseif($into['type'] == 779) {
                        // dossier contactlist - already in use, no action
                    } elseif($into['type'] == 735) {
                        // addressbook contactlist - translate
                        die("check");
                        if(!xarModAPIFunc('dossier','admin','migrate',
                                        array('component' => "taskowner",
                                            'addressbookid' => $contactinfo['id'],
                                            'contactid' => $contactid))) {
                            return;
                        }
                    }                    
//                    echo "owner info: <pre>"; print_r($info); die("</pre>");
                }
            }
        }
    
    xarSessionSetVar('statusmsg', $statusmsg);
    
    $data = xarModAPIFunc('dossier', 'admin', 'menu');

    $data['newcontacts'] = $newcontacts;
    $data['addressbooklist'] = $addressbooklist;
    $data['newcontacts'] = $newcontacts;
        
	return $data;
}

?>
