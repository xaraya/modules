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
        (!isset($type))) {

        $msg = xarML('Wrong arguments to release_userapi_createid.');
        xarErrorSet(XAR_SYSTEM_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }
    /* Get rid of leading and trailing spaces in the name */
    $regname = trim(strtolower($regname));

    // Argument check
    if (!ereg("^[a-z0-9][a-z0-9_-]*[a-z0-9]$", $regname)) {
        $msg = xarML('Registered name may only contain alphanumeric characters, included underscore or hypen, and no spaces.');
        xarErrorSet(XAR_USER_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $releasetable = $xartable['release_id'];

    // Check if that regname exists
    $query = "SELECT xar_rid FROM $releasetable
            WHERE xar_regname = ?
            AND xar_type = ?";

    $result =& $dbconn->Execute($query,array($regname,$type));
    if (!$result) return;

    if ($result->RecordCount() > 0) {
        $msg = xarML('Sorry, requested name for that extension type is already registered.');
        xarErrorSet(XAR_USER_EXCEPTION,
                        'BAD_PARAM',
                        new SystemException($msg));
        return false;
    }

    if (empty($approved)){
        $approved = 1;
    }
    $allrids=array();
    // Get all IDs
    $query2 = "SELECT xar_rid FROM $releasetable ORDER BY xar_rid";

    $result =& $dbconn->Execute($query2);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($rid) = $result->fields;
            $allrids[] = array('rid'=> $rid);
    }
    $result->Close();
    //jojodee - we want to get all the rids that exist and may not be sequential,
    // and allocate first free number to the next rid available for the extension

    $totalrids=count($allrids);
    $i=0;
    $nextid=1;  //We want to start from ID=1 not 0
    for ($i = 0; $i < $totalrids; $i++)
    {
      if ($nextid == ($allrids[$i]['rid'])) {
          $nextid++;
       }
    }
  if ($nextid == 0) return;

    $query = "INSERT INTO $releasetable (
              xar_rid,
              xar_uid,
              xar_regname,
              xar_displname,
              xar_desc,
              xar_type,
              xar_class,
              xar_certified,
              xar_approved,
              xar_rstate
              )
            VALUES (?,?,?,?,?,?,?,?,?,?)";

    $bindvars = array($nextid,$uid,$regname,$displname,$desc,$type,$class,$certified,$approved,$rstate);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;

    // Let any hooks know that we have created a new user.
    xarModCallHooks('item', 'create', $nextid, 'rid');
   $rid=$nextid;
    // Return the id of the newly created user to the calling process
  return $rid;

}

?>