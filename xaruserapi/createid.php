<?php
/**
 * Create an extension ID
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */
function release_userapi_createid($args)
{
    // Get arguments
    extract($args);

    // Argument check
    if ((!isset($regname)) ||
        (!isset($exttype))) {

        $msg = xarML('Wrong arguments to release_userapi_createid.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }
    if (!isset($ridno)) $ridno = 0; //make it zero and check for this before using it for allocations
    $regname = trim(strtolower($regname));
    //get our new registration ID for this extension type
    $regid = xarModAPIFunc('release','user','allocateid',
          array('regname' => $regname,
                'exttype' => $exttype,
                'ridno'     => $ridno));

    if (!($regid)){
      $msg = xarML('<p>Sorry, unable to create an ID for this extension. The name you requested is either already registered for that extension type, or there is a problem with the name format. Please check the name for valid characters, and choose a valid name if necessary to try again.</p>');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM',                       
                    new SystemException($msg));
        return false;
    }
    //we now have our new rid for this extension
    $rid = $regid;

     // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];
    //We want to fill in the missing eids 
    //- this column was copied from the rid column to maintain backward compatiblity when we added new itemtypes
    // so now we have to continue to generate the 'nextid' for this table to fill in gaps

    $alleids=array();
    // Get all EIDs
    $query = "SELECT xar_eid FROM $releasetable ORDER BY xar_eid";

    $result =& $dbconn->Execute($query);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($eid) = $result->fields;
            $alleids[] = array('eid'=> $eid);
    }
    $result->Close();

    $totaleids=count($alleids);
    $i=0;
    $nexteid=1;  //We want to start from ID=1 not 0
    for ($i = 0; $i < $totaleids; $i++)
    {
      if ($nexteid == ($alleids[$i]['eid'])) {
          $nexteid++;
       }
    }
    if ($nexteid == 0) return;


    if (empty($approved)){
        $approved = 1;
    }

    $modified = time();
    $regtime = time();

    $query = "INSERT INTO $releasetable (
              xar_eid,
              xar_rid,
              xar_uid,
              xar_regname,
              xar_displname,
              xar_desc,
              xar_class,
              xar_certified,
              xar_approved,
              xar_rstate,
              xar_regtime,
              xar_modified,
              xar_members,
              xar_scmlink,
              xar_openproj,
              xar_exttype
              )
            VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nexteid,(int)$rid,(int)$uid,$regname,$displname,$desc,$class,$certified,$approved,$rstate,
                      (int)$regtime,(int)$modified,$members,$scmlink,(int)$openproj,(int)$exttype);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $nexteid, 'eid');
    $eid=$nexteid;
    // Return the id of the newly created user to the calling process
  return $eid;

}
?>