<?php
/**
 * Utility function to count the number of items held by this module
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
 * Utility function to count the number of documents held by this module
 * 
 * @author jojodee
 * @returns integer
 * @return number of items held by this module
 * @raise DATABASE_ERROR
 */
function legis_userapi_countitems($args)
{   extract ($args);
    if (isset($defaulthall) && !empty($defaulthall) && !isset($dochall)) {
      $dochall=$defaulthall;
    }
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LegisCompiledTable = $xartable['legis_compiled'];
    $query = "SELECT COUNT(1)
            FROM $LegisCompiledTable";
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
    /* Obtain the number of items */
    list($numitems) = $result->fields;
    $result->Close();
    /* Return the number of items */
    return $numitems;
}
?>
