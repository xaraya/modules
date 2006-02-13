<?php
/**
 * Update an Legis item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */
/**
 * Update an Legis item
 *
 * @author jojodee
 * @param  $args ['cdid'] the system ID of the document
 * @param  $args ['cdtitle'] the Title of the item
 * @param  $args ['mdid'] the master doc type id
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function legis_adminapi_update($args)
{
    extract($args);

    $invalid = array();
    if (!isset($cdid) || !is_numeric($cdid)) {
        $invalid[] = 'item ID';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'update', 'legis');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $notifytype=0; //variable to hold email notification type;
    $item = xarModAPIFunc('legis','user','get',array('cdid' => $cdid));
    /*Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    if (!xarSecurityCheck('EditLegis', 1, 'Item', "All:All:$cdid")) {
        return;
    }
    /* What do we need to update here and what should not be allowed for update
       Let's go through existing item information to start with */
    // cdid - set by system never changes

    // mdid - set at document creation - should never change
    if (!isset($mdid)) $mdid=$item['mdid'];
    // cdnum - set when document doc status is set to Valid and not changed after that ie > 1
    //         not set from UI - need rules for this


    // cdtitle - editable until Veto status is set ie > 0
    if (!isset($cdtitle)) $cdtitle=$item['cdtitle'];
    if (!isset($contributors)) $contributors=$item['contributors'];
    // doccontent - editable until Veto status set  to  > 0
    if (!isset($doccontent)) $doccontent=$item['doccontent'];
    if (!isset($dochall)) $dochall=$item['dochall'];
    // pubnotes - editable
    if (!isset($pubnotes)) $pubnotes=$item['pubnotes'];

    if ($item['vetostatus'] > 0 ){
       $cdtitle = $item['cdtitle'];
       $doccontent = $item['doccontent'];
    }
    // dochall - not editable once the document has doc status changed (as cdnum is set then)
    // docstatus  - editable to set - not editable (due to doc number allocation)
    if (!isset($docstatus)) {
        $docstatus =$item['docstatus'];

    }
    if (($docstatus ==2)&& ($docstatus <> $item['docstatus'])){ //it has changed  from pending to valid or not valid
        $reviewdate =time();
        //We need to allocate a cdnum as well
        $notifytype =2;
        $cdnum=xarModAPIFunc('legis','admin','makedocnum',array('cdid'=>(int)$cdid,'mdid'=>(int)$mdid,'dochall'=>(int)$dochall));
    } elseif (($docstatus ==3)&& ($docstatus <> $item['docstatus'])){
        $reviewdate =time();
        $dochall = $item['dochall'];
        $cdnum=$item['cdnum'];
       $notifytype =2;
    } else {
        $docstatus = $item['docstatus'];
        $dochall = $item['dochall'];
        $cdnum=$item['cdnum'];
        $reviewdate =$item['reviewdate']; //whatever is there by default
    }
    if (!isset($votestatus)) $votestatus=$item['votestatus'];
    if (($votestatus >0)&& ($votestatus <> $item['votestatus'])){ //it has changed  from pending to passed
       $passdate=time();
       if ($votestatus == 1) {
           $notifytype =4;
       } else {
           $notifytype =5;
       }
    } else {
      $passdate=$item['passdate'];
      $votestatus=$item['votestatus'];
    }
    // votestatus - editable to set - then not editable (cept by top admin and until veto status set)
    // vetostatus - editable to set - then fixed (cept by top admin)
    if (!isset($vetostatus)) $vetostatus=$item['vetostatus'];
    if ($item['vetostatus'] > 0 ){
       $votestatus = $item['votestatus'];
       $vetostatus = $item['vetostatus'];
    } elseif (($vetostatus >0)&& ($vetostatus <> $item['vetostatus'])){ //it has changed  from pending to veto or not veto
       $vetodate =time();
       if ($vetostatus == 1) {
          $notifytype =6;
       }else {
          $notifytype =7;
       }
    } else {
       $vetodate=$item['vetodate'];
       $vetostatus=$item['vetostatus'];
    }
    // all status dates set at time the status is set

  // archiveswitch - turn on for achive state
    if (isset($archswitch) && $archswitch >0 && $archswitch <> $item['archswitch']) {
       $archivedate =time();
       $archswitch=1;
    } else {
       $archivedate = $item['archivedate'];
       $archswitch = 0;
    }
    //archivedate - when archived


   // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $LegisCompiledTable = $xarTables['legis_compiled'];


    $query = "UPDATE  $LegisCompiledTable
              SET    xar_cdnum =?,
                     xar_cdtitle =?,
                     xar_docstatus=?,
                     xar_votestatus=?,
                     xar_vetostatus=?,
                     xar_contributors=?,
                     xar_doccontent=?,
                     xar_reviewdate=?,
                     xar_vetodate=?,
                     xar_archivedate=?,
                     xar_archswitch=?,
                     xar_pubnotes=?,
                     xar_dochall=?
              WHERE xar_cdid = ?";
    $bindvars = array((int)$cdnum, $cdtitle, $docstatus, $votestatus, $vetostatus, $contributors, $doccontent,
                       $reviewdate, $vetodate, $archivedate, $archswitch, $pubnotes, $dochall, $cdid);


    $result = &$dbconn->Execute($query,$bindvars);

    if (!$result) return;
    $item['module'] = 'legis';
    $item['itemid'] = $cdid;
    $item['cdtitle'] = $cdtitle;
    $item['mdid'] = $mdid;
    xarModCallHooks('item', 'update', $cdid, $item);

    //Let's tell all hall members that we have a modified document
    //notifytype 1 = new document, 2 = validated, 3 = invalidated, 4 = passed, 5 = notpassed, 6 = notvetoed 7 = vetoed 8 = deleted
    if (!xarModAPIFunc('legis','user','notify',
                           array('notifytype'   => (int)$notifytype,
                                 'cdid'         => (int)$cdid))) return;
    /* Let the calling process know that we have finished successfully */
    return true;
} 
?>
