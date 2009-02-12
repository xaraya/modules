<?php
/**
 * AccessMethods Module - A Contact and Customer Service Management Module
 *
 * @package modules
 * @copyright (C) 2002-2007 Chad Kraeft
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage AccessMethods Module
 * @link http://xaraya.com/index.php/release/829.html
 * @author St.Ego
 */
function dossier_user_vcard($args)
{
    extract($args);
    if (!xarVarFetch('contactid', 'id', $contactid)) return;

    $data = xarModAPIFunc('dossier','user','menu');
    $data['contactid'] = $contactid;

    $item = xarModAPIFunc('dossier',
                          'user',
                          'get',
                          array('contactid' => $contactid));

    if (!isset($item)) {
        $msg = xarML('Not authorized to access #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION',
                       new SystemException(__FILE__.'('.__LINE__.'): '.$msg));
        return $msg;
    }
    
    $data['status'] = '';
    if($item['sortname'] == "[contact deleted]") {
        $data['status'] = $item['sortname'];
    }
    
    $data['item'] = $item;
    
    $mycontactid = xarModAPIFunc('dossier','user','mycontactid');
    
    if($mycontactid) {
        $data['isfriend'] = xarModAPIFunc('dossier','friendslist','get',array('contactid' => $mycontactid, 'friendid' => $contactid));
    
        $data['friendlist'] = xarModAPIFunc('dossier','friendslist','getall',array('contactid' => $contactid, 'private' => 0));
    } else {
        $data['isfriend'] = false;
    
        $data['friendlist'] = array();
    }
    
    $data['addresslist'] = xarModAPIFunc('dossier', 'locations', 'getallcontact', array('contactid' => $contactid));

    $data['hookoutput'] = array();

    $hooks = xarModCallHooks('item',
                             'display',
                             $contactid,
                             xarModURL('dossier',
                                       'admin',
                                       'display',
                                       array('contactid' => $contactid)));
    if (!empty($hooks)) {
        $data['hookoutput'] = $hooks;
    }
  
//    echo "<pre>"; print_r($item); echo "</pre>";

    Header("Content-Type: text/x-vCard");
	echo "BEGIN:VCARD\n";
	echo "VERSION:2.1\n";
	echo "N:".$item['lname'].";".$item['fname'].";;;;\n";
	echo "FN:".$item['sortname']."\n";
    if(!empty($item['company'])) {
        echo "ORG:".$item['company']."\n";
    }
    if(!empty($item['title'])) {
        echo "TITLE:".$item['title']."\n";
    }
	echo "TEL;HOME;VOICE:".$item['phone_home']."\n";
	echo "TEL;CELL;VOICE:".$item['phone_cell']."\n";
	echo "TEL;WORK;VOICE:".$item['phone_work']."\n";
	echo "EMAIL;PREF;INTERNET:".$item['email_2']."\n";
	echo "EMAIL;PREF;INTERNET:".$item['email_1']."\n";
	echo "BDAY:".$item['dateofbirth']."\n";
	echo "END:VCARD\n";

    die();
    
//    return $data;
}
?>
