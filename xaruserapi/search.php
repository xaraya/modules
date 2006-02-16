<?php
/**
 * Get a specific item
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */

/**
 * Searches all releases
 *
 * @author jojodee
 * @access private
 * @returns mixed description of return
 */
function legis_userapi_search($args)
{
    $legisdocs = array();
    if (empty($args) || count($args) <= 1 ) {
        return false;
    }
    extract($args);

     if($q == ''){
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $LegisCompiledTable = $xartable['legis_compiled'];
    $where = '';

    $sql = "SELECT  xar_cdid,
                    xar_cdnum,
                    xar_cdtitle,
                    xar_contributors,
                    xar_doccontent,
                    xar_dochall
              FROM  $LegisCompiledTable
              WHERE  (";

    $bindvars = array();

    if (isset($cdnum)) {
        $sql .= "xar_cdnum = ?";
        $bindvars[] = $cdnum;
    }

   if (isset($cdtitle)) {
        if (isset($cdnum)) {
            $sql .= " OR ";
        }
        $sql .= " xar_cdtitle LIKE ?";
        $bindvars[] = '%'.$cdtitle.'%';
    }
    if (isset($doccontent)) {
        if (isset($cdnum) || isset($cdtitle)) {
            $sql .= " OR ";
        }
        $sql .= " xar_doccontent LIKE ?";
        $bindvars[] = '%'.$doccontent.'%';
    }
    if (isset($contributors)) {
        if (isset($cdnum) || isset($cdtitle) || isset($doccontent)) {
            $sql .= " OR ";
        }
        $sql .= " xar_contributors LIKE ?";
        $bindvars[] = '%'.$contributors.'%';
    }
    if (isset($dochall)) {
          if (isset($cdnum) || isset($cdtitle) || isset($doccontent) || isset($contributors)) {
            $sql .= " OR ";
        }
        $sql .= " xar_dochall = ?";
        $bindvars[] = $dochall;
    }

    if (isset($docstatus)) {
          if (isset($cdnum) || isset($cdtitle) || isset($doccontent) || isset($contributors) || isset($dochall)) {
            $sql .= " OR ";
        }
        $sql .= " xar_docstatus = 1 OR xar_docstatus = 2 ";
    }

    if (!isset($docstatus)){
           if (isset($cdnum) || isset($cdtitle) || isset($doccontent) || isset($contributors) || isset($dochall)) {
            $sql .= " AND xar_docstatus = 2 ";
        }
        //$sql .= " xar_docstatus = 2";
    }

    $sql .= ")  ORDER BY xar_cdid ASC";

    $result =& $dbconn->Execute($sql, $bindvars);

    if (!$result) return;
    // no results to return .. then return them :p
    if ($result->EOF) {
        return array();
    }
    for (; !$result->EOF; $result->MoveNext()) {
        list($cdid, $cdnum, $cdtitle, $contributors, $doccontent, $dochall) = $result->fields;
        if (xarSecurityCheck('ReadLegis', 0)) {
            $legisdocs[] = array('cdid' => $cdid,
                                'cdnum' => $cdnum,
                                'cdtitle' => $cdtitle,
                                'contributors' => $contributors,
                                'doccontent' => $doccontent,
							    'dochall' => $dochall );
        }
    }
    $result->Close();

    // Return the releases
    return $legisdocs;

}
?>