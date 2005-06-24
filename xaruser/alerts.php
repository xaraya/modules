<?php
/**
 * File: alerts.php $Id:
 * 
 * Function that determines which categorical events this user wants to be alerted about via email.
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2004 by Metrostat Technologies, Inc.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.metrostat.net
 *
 * @subpackage julian
 * initial template: Roger Raymond
 * @TODO Link alerts to hooked categories
 * @author Jodie Razdrh/John Kevlin/David St.Clair
 */
function julian_user_alerts()
{
    if (!xarVarFetch('action','str',$action,'')) return;
    //Get the categories from the form.
    if (!xarVarFetch('cats','array',$cats,array())) return;
    // TODO: Where is this good for? remove?
    if (!xarVarFetch('cal_date','int:0:8',$cal_date,date("Ymd"))) return;

    // Security check - important to do this as early as possible to avoid
    // potential security holes or just too much wasted processing
    if (!xarSecurityCheck('Viewjulian')) return; 

    /*// not neccecary
    // Load up database
    $dbconn =& xarDBGetConn();
    //get db tables
    $xartable = xarDBGetTables();
    $categories_table = $xartable['julian_categories'];
    $alerts_table     = $xartable['julian_alerts'];
    */
   
    //store the categories the user has selected for alerts
    if (!strcmp($action,'update')) {
        /*// use an API function for this
        $sql = "DELETE FROM " . $alerts_table . " WHERE uid = '".xarUserGetVar('uid')."';";
        $rs = $dbconn->Execute($sql);
        //only add an entry to the alerts table if the user has not unsubscribed to all alerts
        if (!empty($cats)) {
            $uid = xarUserGetVar('uid');
              $subscriptions = serialize($cats);
            $sql = "INSERT INTO " . $alerts_table . " SET uid= ?, subscriptions= ? ;";
            $bindvars = array ($uid, $subscriptions);
            $rs = $dbconn->Execute($sql, $bindvars);
        }
        */
        // need to check data?
        $subscriptions = $cats;
        // store updated configuration
        xarModAPIFunc('julian','user','updatesubscriptions', $subscriptions);
        
        //redirect the user back to the previous page
        $back_link = xarSessionGetVar('lastview');
        xarResponseRedirect($back_link);
    }
        
    /*// ModUserVar's would be better
    $checkboxes = array();
    //Get the boxes that are already checked for this user if any
    $sql = "SELECT * FROM " . $alerts_table . " WHERE uid='".xarUserGetVar('uid')."';";
    $rs = $dbconn->Execute($sql);
    $checkboxObj = $rs->FetchObject(false);
    if ($rs->NumRows() > 0)
      $checkboxes = unserialize($checkboxObj->subscriptions);
    */
    $useralerts = xarModAPIFunc('julian','user','getsubscriptions');
  
    /*// use an API function for this
    //Get the Categories and see if they are checked for this user.
    $sql = "SELECT cat_id,cat_name FROM " . $categories_table ." ORDER BY list_index,cat_name";
    $rs = $dbconn->Execute($sql);
    $categories = array();
    while (!$rs->EOF) {
        $catObj = $rs->FetchObject(false);
        $categories[$catObj->cat_id] = in_array($catObj->cat_id,$checkboxes)? 'checked'  : '';
        $cat_name[$catObj->cat_id]=$catObj->cat_name;
        $rs->MoveNext();
    }
    */   
    $categories = xarModAPIFunc('julian','user','getcategories');
    foreach ($categories as $cid => $info) {
       $categories[$cid]['checked'] = in_array($cid, $useralerts) ? true  : false;
    }
    
    $data = array();
    $data['categories'] = $categories;
    // TODO: Where is this good for? remove?
    $data['cal_date']   = $cal_date;
    
    return $data;
}
?>
