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
function dossier_adminapi_create($args)
{
    extract($args);

    $invalid = array();
    if ( (!isset($fname) || !is_string($fname) || empty($fname))
        && (!isset($lname) || !is_string($lname) || empty($lname))
        && (!isset($company) || !is_string($company) || empty($company))
        && (!isset($sortname) || !is_string($sortname) || empty($sortname))
        ) {
        $invalid[] = 'contact name/company';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                    join(', ',$invalid), 'admin', 'create', 'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

/* MUST BE OPEN TO ALLOW CREATION OF RECORDS BY ANON DURING REGISTRATION
    if (!xarSecurityCheck('TeamDossierAccess', 1, 'Item', $item[cat_id].":".$item[userid].":".$item[company].":".$item[agentuid]) && false) {
        $msg = xarML('Not authorized to add #(1) items',
                    'dossier');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'NO_PERMISSION', new SystemException($msg));
        return;
    }
  */  
    if(!isset($sortname)) $sortname = "";
    if(!isset($lname)) $lname = "";
    if(!isset($lname)) $lname = "";
    if(!isset($sortcompany)) $sortcompany = "";
    if(!isset($company)) $company = "";
  
    if(empty($sortname)) {
        $sortname = $lname.(!empty($lname) && !empty($fname) ? ", " : "").$fname;
    }
    
    // need to strip prefixes [a|an|the]
    if(empty($sortcompany)) {
        $sortcompany = $company ? $company : "";
    }
    
    $dbconn =& xarDBGetConn();
    $xartable = xarDBGetTables();

    $contacts_table = $xartable['dossier_contacts'];

    $nextId = $dbconn->GenId($contacts_table);

    $query = "INSERT INTO $contacts_table (
                  contactid,
                  cat_id,
                  agentuid,
                  userid,
                  private,
                  contactcode,
                  prefix,
                  lname,
                  fname,
                  sortname,
                  dateofbirth,
                  title,
                  company,
                  sortcompany,
                  img,
                  phone_work,
                  phone_cell,
                  phone_fax,
                  phone_home,
                  email_1,
                  email_2,
                  chat_AIM,
                  chat_YIM,
                  chat_MSNM,
                  chat_ICQ,
                  contactpref,
                  notes,
                  datecreated,
                  datemodified)
            VALUES (?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,?,?,?, ?,?,NOW(),NOW() )";

    $bindvars = array(
              $nextId,
              isset($cat_id) ? $cat_id : 0,
              isset($agentuid) ? $agentuid : 0,
              isset($userid) ? $userid : 0,
              isset($private) ? $private : 1,
              isset($contactcode) ? $contactcode : "",
              isset($prefix) ? $prefix : "",
              $lname,
              $fname,
              $sortname,
              isset($dateofbirth) ? $dateofbirth : "",
              isset($title) ? $title : "",
              $company,
              $sortcompany,
              isset($img) ? $img : "",
              isset($phone_work) ? $phone_work : "",
              isset($phone_cell) ? $phone_cell : "",
              isset($phone_fax) ? $phone_fax : "",
              isset($phone_home) ? $phone_home : "",
              isset($email_1) ? $email_1 : "",
              isset($email_2) ? $email_2 : "",
              isset($chat_AIM) ? $chat_AIM : "",
              isset($chat_YIM) ? $chat_YIM : "",
              isset($chat_MSNM) ? $chat_MSNM : "",
              isset($chat_ICQ) ? $chat_ICQ : "",
              isset($contactpref) ? $contactpref : "",
              isset($notes) ? $notes : "");
              
    $result = &$dbconn->Execute($query,$bindvars);
    if (!$result) return;
//echo $query."<pre>";print_r($bindvars);die("</pre>");
// PRIVATE INITIALLY SET BASED ON USER PREFERENCE


    $contactid = $dbconn->PO_Insert_ID($contacts_table, 'contactid');

    // is this already hooked? if so, don't cascade it...
    if(!isset($hooked) || $hooked != 1) {
        $item = $args;
        $item['module'] = 'dossier';
        $item['itemid'] = $contactid;
        xarModCallHooks('item', 'create', $contactid, $item);
    }
    
    return $contactid;
}

?>
