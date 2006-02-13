<?php
/**
 * Allocate a new document number
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
 * @param  $args ['dochall'] the Title of the item
 * @param  $args ['mdid'] the master doc type id
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function legis_adminapi_makedocnum($args)
{ 
   //We want to allocate a number based uniquely on the hall and the document type
    extract($args);

    $invalid = array();
    if (!isset($mdid) || !is_numeric($mdid)) {
        $invalid[] = 'mdid';
    }
    if (!isset($dochall) || !is_numeric($dochall)) {
        $invalid[] = 'dochall';
    }

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'makedocnum', 'legis');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    //Get all documents with cdnums already (valid status) for the same hall and document type
//    $item = xarModAPIFunc('legis','user','getall',array('mdid'=>$mdid, 'dochall'=>$dochall, 'docstatus'=>2));

    /*Check for exceptions */
  //  if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

   // Get datbase setup
    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $LegisCompiledTable = $xarTables['legis_compiled'];


   $allcdnums=array();
    // Get all the existing CDNUMS
    $query = "SELECT xar_cdnum
              FROM $LegisCompiledTable
              WHERE xar_mdid = ? AND xar_dochall = ? AND xar_docstatus = 2 AND xar_cdnum > 0
              ORDER BY xar_cdnum";

    $bindvars = array($mdid,$dochall);
    $result =& $dbconn->Execute($query,$bindvars);
    if (!$result) return;
    for (; !$result->EOF; $result->MoveNext()) {
        list($cdnum) = $result->fields;
            $allcdnums[] = array('cdnum'=> $cdnum);
    }
    $result->Close();
    //jojodee - we want to get all the cdnums that exist and may not be sequential,
    // and allocate first free number to the next cdnum available

    $totalcdnums=count($allcdnums);
    $i=0;
    $nextid=1;  //We want to start from ID=1 not 0
    for ($i = 0; $i < $totalcdnums; $i++)
    {
      if ($nextid == ($allcdnums[$i]['cdnum'])) {
          $nextid++;
       }
    }
var_dump($nextid);
     /* Let the calling process know that we have finished successfully */
    return $nextid;
}
?>
