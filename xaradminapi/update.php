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
function dossier_adminapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($contactid) || !is_numeric($contactid)) {
        $invalid[] = 'Contact ID';
    }
    
    if ( (!isset($fname) || !is_string($fname) || empty($fname))
        && (!isset($lname) || !is_string($lname) || empty($lname))
        && (!isset($company) || !is_string($company) || empty($company))
        ) {
        $invalid[] = 'contact name/company';
    }
    
//    if (!isset($sortname) || !is_string($sortname)) {
//        $invalid[] = 'sortname';
//    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'DOSSIER');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    
    if(empty($sortname)) {
        $sortname = $lname.(!empty($lname) && !empty($fname) ? ", " : "").$fname;
    }
    
    // need to strip prefixes [a|an|the]
    if(empty($sortcompany)) {
        $sortcompany = $company;
    }

    $item = xarModAPIFunc('dossier',
                            'user',
                            'get',
                            array('contactid' => $contactid));

    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('ClientDossierAccess', 1, 'Contact', $item['cat_id'].":".$item['userid'].":".$item['company'].":".$item['agentuid'])) {
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $contactstable = $xartable['dossier_contacts'];

    $query = "UPDATE $contactstable
            SET cat_id = ?,
                  agentuid = ?,
                  userid = ?,
                  private = ?,
                  contactcode = ?,
                  prefix = ?,
                  lname = ?,
                  fname = ?,
                  sortname = ?,
                  dateofbirth = ?,
                  title = ?,
                  company = ?,
                  sortcompany = ?,
                  img = ?,
                  phone_work = ?,
                  phone_cell = ?,
                  phone_fax = ?,
                  phone_home = ?,
                  email_1 = ?,
                  email_2 = ?,
                  chat_AIM = ?,
                  chat_YIM = ?,
                  chat_MSNM = ?,
                  chat_ICQ = ?,
                  contactpref = ?,
                  notes = ?,
                  datemodified = NOW()
            WHERE contactid = ?";

    $bindvars = array(
              isset($cat_id) ? $cat_id : $item['cat_id'],
              isset($agentuid) ? $agentuid : $item['agentuid'],
              $userid,
              $private,
              $contactcode,
              $prefix,
              $lname,
              $fname,
              $sortname,
              $dateofbirth,
              $title,
              $company,
              $sortcompany,
              $img,
              $phone_work,
              $phone_cell,
              $phone_fax,
              $phone_home,
              $email_1,
              $email_2,
              $chat_AIM,
              $chat_YIM,
              $chat_MSNM,
              $chat_ICQ,
              $contactpref,
              $notes,
              $contactid);
              
    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) {return;}
    
    if(!isset($hooked) || $hooked != 1) {
        $item['module'] = 'dossier';
        $item['itemid'] = $contactid;
        $item['name'] = $company;
        xarModCallHooks('item', 'update', $contactid, $item);
    }
    
    return true;
}
?>
