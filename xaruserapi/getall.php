<?php
/**
 * Get all legis items
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Get all legis items
 * 
 * @author jojodee
 * @param numitems $ the number of items to retrieve (default -1 = all)
 * @param startnum $ start with this item number (default 1)
 * @returns array
 * @return array of items, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function legis_userapi_getall($args)
{
  extract($args);

    if (isset($defaulthall) && !empty($defaulthall) && !isset($dochall)) {
      $dochall=$defaulthall;
    }
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    $invalid = array();
    if (!isset($startnum) || !is_numeric($startnum)) {
        $invalid[] = 'startnum';
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $invalid[] = 'numitems';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'getall', 'legis');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    $items = array();
    if (!xarSecurityCheck('ViewLegis')) return;

    $dbconn =& xarDBGetConn();
    $xarTables =& xarDBGetTables();

    $LegisCompiledTable = $xarTables['legis_compiled'];

    $query = "SELECT xar_cdid,
                     xar_mdid,
                     xar_cdnum,
                     xar_cdtitle,
                     xar_docstatus,
                     xar_votestatus,
                     xar_vetostatus,
                     xar_submitdate,
                     xar_submitter,
                     xar_reviewdate,
                     xar_passdate,
                     xar_vetodate,
                     xar_archivedate,
                     xar_archswitch,
                     xar_contributors,
                     xar_doccontent,
                     xar_pubnotes,
                     xar_dochall
             FROM $LegisCompiledTable ";


    $bindvars=array();
    if (isset($docstatus) || isset($dochall) || isset($mdid)) {
     $query .= " WHERE ";
    }

    if (isset($docstatus) && !empty($docstatus)) {
      $query .= " xar_docstatus =? ";
         $bindvars[]=(int)$docstatus;

    }
    if (isset($dochall) && !empty($dochall)){
        if (!empty($docstatus)) {
           $query .= " AND xar_dochall = ?";
        } else {
             $query .= " xar_dochall = ?";
        }
        $bindvars[]=(int)$dochall;
    }

    if (isset($mdid) && !empty($mdid)){
      if (empty($docstatus) && empty($dochall)) {
           $query .= " xar_mdid = ?";
      } elseif (!empty($docstatus) || !empty($dochall))  {
             $query .= " AND xar_mdid = ?";
      }
        $bindvars[]=(int)$mdid;
    }

    $query .=" ORDER BY xar_cdnum";

    $result = $dbconn->SelectLimit($query, $numitems, $startnum-1,$bindvars);

    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cdid,$mdid,$cdnum,$cdtitle,$docstatus,$votestatus,$vetostatus,$submitdate,
            $submitter,$reviewdate,$passdate,$vetodate,$archivedate,$archswitch,$contributors,
            $doccontent,$pubnotes,$dochall) = $result->fields;
        if (xarSecurityCheck('Viewlegis', 0, 'Item', "$cdtitle:All:$cdid")) {
            $items[] = array('cdid'         => $cdid,
                             'mdid'         => $mdid,
                             'cdnum'        => $cdnum,
                             'cdtitle'      => $cdtitle,
                             'docstatus'    => $docstatus,
                             'votestatus'   => $votestatus,
                             'vetostatus'   => $vetostatus,
                             'submitdate'   => $submitdate,
                             'submitter'    => $submitter,
                             'reviewdate'   => $reviewdate,
                             'passdate'     => $passdate,
                             'vetodate'     => $vetodate,
                             'archivedate'  => $archivedate,
                             'archswitch'   => $archswitch,
                             'contributors' => $contributors,
                             'doccontent'   => $doccontent,
                             'pubnotes'     => $pubnotes,
                             'dochall'      => $dochall);
        }
    }
    $result->Close();
    /* Return the items */
    return $items;
}
?>