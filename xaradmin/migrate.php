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
include_once ('modules/dossier/xarglobal.php');
/**
 * view items
 */
function dossier_admin_migrate()
{
    if (!xarVarFetch('action', 'str::', $action, '', XARVAR_NOT_REQUIRED)) return;
            
    if (!xarSecurityCheck('AdminDossier', 0, 'Item', "All:All:All")) {//TODO: security
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return;
    }
    
    $ttl_existing_contacts = xarModAPIFunc('dossier', 'user', 'countitems');

    $addressbooklist = xarModAPIFunc('dossier', 'user', 'getall_addressbook');
    
    if (!isset($addressbooklist) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
//echo "addressbooklist: ".count($addressbooklist)."<pre>"; print_r($addressbooklist); die("</pre>");
    /* TODO: IMPORT CUSTOM FIELD DATA
    $custFields = xarModAPIFunc('addressbook','user','getcustfieldinfo',array('flag'=>_AB_CUST_ALLFIELDINFO));
    foreach($custFields as $custField) {
        $abData[$custField['colName']] = '';
    }
        foreach($custFields as $custField) {
            if ($custField['custDisplay']) {
                switch ($custField['custType']) {
                    case 'int(1) default NULL':
                        if ($$custField['colName']) {
                            $displayRow[] = '<acronym title="'.$custField['custLabel'].'">'.$custField['custShortLabel'].'</acronym>';
                        } else {
                            $displayRow[] = "&nbsp;";
                        }
                        break;
                    default:
                        $displayRow[] = $$custField['colName'];
                        break;
                }
            }
        }
    */
    
    $statusmsg = "Addressbook Contacts Imported:";
    $newcontacts = array();
    $labels = xarModAPIFunc('addressbook','util','getitems',array('tablename'=>'labels'));
    
    foreach($addressbooklist as $contactinfo) {
//echo "test: <pre>"; print_r($contactinfo);die("</pre>");
        // determine if entry already exists
        // force selection of duplicates via a radio button for each, defaulted to the existing entry
        // migrate to fill empty fields when found
        // only do [10|20|40] at a time
        if($ttl_existing_contacts > 0) { 
            if(!empty($contactinfo['fname']) || !empty($contactinfo['lname'])) {
                // only for if contacts already exist in dossier during migration
                // can be avoided if just migrating fresh addressbook items
                $contactlist = xarModAPIFunc('dossier','user','getall',
                                                array('lname' => $contactinfo['lname'],
                                                    'fname' => $contactinfo['fname']));
            } elseif(!empty($contactinfo['company'])) {
                $contactlist = xarModAPIFunc('dossier','user','getall',
                                                array('company' => $contactinfo['company']));
            } else {
                $contactlist = array();
            }
        } else {
            $contactlist = array();
        }
//echo "contactlist: <pre>"; print_r($contactlist);die("</pre>");
                                  
        if ((!isset($contactlist) || !is_array($contactlist)) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
        
        if(count($contactlist) <= 0) {
            // TODO: translate the cat_id from addressbook cat list to dossier cat list
            // TODO: consolidate extra fields into the Notes field
            // split up contact_# fields appropriately
            $notes = $contactinfo['note'];
            $contactinfo['phone_work'] = "";
            $contactinfo['phone_cell'] = "";
            $contactinfo['phone_fax'] = "";
            $contactinfo['phone_home'] = "";
            $contactinfo['email_1'] = "";
            $contactinfo['chat_AIM'] = "";
            $contactpref = "";
            $contactid = false;
            
            for($x=1;$x<=5;$x++) {
                $fieldinfo = "c_label_".$x;
                $fieldname = "c_labelname_".$x;
                $datainfo = "contact_".$x;
                
                if(!empty($contactinfo[$datainfo])) {
                    
                    foreach ($labels as $lab) {
                        if ($contactinfo[$fieldinfo] == $lab['id']) {
                            $contactinfo[$fieldname] = xarVarPrepHTMLDisplay($lab['name']);
                        }
                    }
                    switch($contactinfo[$fieldname]) {
                        case "Fax":
                            $contactinfo['phone_fax'] = $contactinfo[$datainfo];
                            break;
                        case "Home":
                            $contactinfo['phone_home'] = $contactinfo[$datainfo];
                            $contactpref = !empty($contactinfo[$datainfo]) ? "phone_home" : $contactpref;
                            break;
                        case "E-Mail":
                            $contactinfo['email_1'] = $contactinfo[$datainfo];
                            $contactpref = !empty($contactinfo[$datainfo]) ? "email_1" : $contactpref;
                            break;
                        case "Work":
                            $contactinfo['phone_work'] = $contactinfo[$datainfo];
                            $contactpref = !empty($contactinfo[$datainfo]) ? "phone_work" : $contactpref;
                            break;
                        case "Mobile":
                            $contactinfo['phone_cell'] = $contactinfo[$datainfo];
                            $contactpref = !empty($contactinfo[$datainfo]) ? "phone_cell" : $contactpref;
                            break;
                        case "URL":
                            $notes .= $contactinfo[$datainfo];
                            break;
                        case "AIM":
                            $contactinfo['chat_AIM'] = $contactinfo[$datainfo];
                            break;
                        case "Skype":
                            $notes .= $contactinfo[$datainfo];
                            break;
                        case "Other":
                            $notes .= $contactinfo[$datainfo];
                            break;
                    }
                }
            }
//if($contactinfo['fname'] == "Chad") {
 //   echo "contactinfo: <pre>"; print_r($contactinfo); die("</pre>");
//    }
            $notes .= $contactinfo['custom_1'].$contactinfo['custom_2'].$contactinfo['custom_3'].$contactinfo['custom_4'];
            
            $contactid = xarModAPIFunc('dossier','admin','create',
                                    array('cat_id'      => $contactinfo['cat_id'],
                                       'agentuid'        => 0,
                                       'userid'         => $contactinfo['user'],
                                       'private'        => $contactinfo['private'],
                                       'contactcode'    => "",
                                       'prefix'         => $contactinfo['prefix'],
                                       'lname'          => $contactinfo['lname'],
                                       'fname'          => $contactinfo['fname'],
                                       'sortname'       => $contactinfo['sortname'],
                                       'dateofbirth'    => "",
                                       'title'          => $contactinfo['title'],
                                       'company'        => $contactinfo['company'],
                                       'sortcompany'    => $contactinfo['sortcompany'],
                                       'img'            => $contactinfo['img'],
                                       
                                       'phone_work'     => $contactinfo['phone_work'],
                                       'phone_cell'     => $contactinfo['phone_cell'],
                                       'phone_fax'      => $contactinfo['phone_fax'],
                                       'phone_home'     => $contactinfo['phone_home'],
                                       'email_1'        => $contactinfo['email_1'],
                                       'email_2'        => "",
                                       'chat_AIM'       => $contactinfo['chat_AIM'],
                                       'chat_YIM'       => "",
                                       'chat_MSNM'      => "",
                                       'chat_ICQ'       => "",
                                       
                                       'contactpref'    => $contactpref,
                                       
                                       'notes'          => $notes));


            if (!isset($contactid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
            
            if(!xarModAPIFunc('dossier','admin','linkaddressbook',array('contactid' => $contactid, 'addressbook_id' => $contactinfo['id']))) {return;}
            
            $contactinfo['contactid'] = $contactid;
//            echo "test: <pre>"; print_r($contactinfo); die("</pre>");
            if(!empty($contactinfo['address_1']) || !empty($contactinfo['city']) || !empty($contactinfo['state'])) {
                $locationid = xarModAPIFunc('dossier','locations','create',
                                            array('address_1' => $contactinfo['address_1'],
                                                'address_2' => $contactinfo['address_2'],
                                                'city' => $contactinfo['city'],
                                                'us_state' => $contactinfo['state'],
                                                'postalcode' => $contactinfo['zip'],
                                                'country' => $contactinfo['country']));


                if (!isset($locationid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;
                
                if(!xarModAPIFunc('dossier','locations','createdata',array('contactid' => $contactid, 'locationid' => $locationid))) { return; }
            }
            
            $newcontacts[$contactinfo['id']] = $contactinfo;
            $statusmsg .= "<br>New Contact: ".$contactinfo['fname']." ".$contactinfo['lname']." @ ".$contactinfo['company'];
        }
    }
    
    $statusmsg .= "<br><br>Total New Contacts: ".count($newcontacts);
    
    xarSessionSetVar('statusmsg', $statusmsg);
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "projectclient"))) return;
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "projectagent"))) return;
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "projectteam"))) return;
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "taskcreator"))) return;
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "taskowner"))) return;
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "taskassigner"))) return;
   
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "taskreminder"))) return;
    
    if(!xarModAPIFunc('dossier','admin','migrate',array('component' => "taskworklog"))) return;
    
    $data = xarModAPIFunc('dossier', 'admin', 'menu');

    $data['newcontacts'] = $newcontacts;
    $data['addressbooklist'] = $addressbooklist;
    $data['newcontacts'] = $newcontacts;
        
	return $data;
}

?>
