<?php
/**
 * Categories module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Categories Module
 * @link http://xaraya.com/index.php/release/147.html
 * @author Categories module development team
 */
/**
 * @return int count
 */
function categories_userapi_countitems_deprecated($args)
{
    // Get arguments from argument array
    extract($args);

    // Optional arguments
    if (!isset($cids)) {
        $cids = array();
    }

    // Security check
    if(!xarSecurityCheck('ViewCategoryLink')) return;

    // Get database setup
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $categorieslinkagetable = $xartable['categories_linkage'];

    // Check if we have active CIDs
    $bindvars = array();
    if (count($cids) > 0) {
        // We do.  We just need to know how many articles there are in these
        // categories
        // Get number of links with those categories in cids
        // TODO: make sure this is SQL standard
        //$sql = "SELECT DISTINCT COUNT(xar_iid)
        if($dbconn->databaseType == 'sqlite') {
        $sql = "SELECT COUNT(*)
                FROM (SELECT DISTINCT xar_iid  FROM $categorieslinkagetable "; //unbalanced
        }else{
        $sql = "SELECT COUNT(DISTINCT xar_iid)
                FROM $categorieslinkagetable ";
        }
        if (isset($table) && isset($field) && isset($where)) {
            $sql .= "LEFT JOIN $table ON $field = xar_iid;";
        }
        $sql .= "  WHERE ";

        $allcids = join(', ', $cids);
        $bindmarkers - '?' . str_repeat(',?',count($cids)-1);
        $bindvars = $cids;
        $sql .= "xar_cid IN ($bindmarkers) ";

        if (isset($table) && isset($field) && isset($where)) {
            $sql .= " AND $where ";
        }
        // Balance parentheses
       if($dbconn->databaseType == 'sqlite') $sql .=')';
        $result = $dbconn->Execute($sql,$bindvars);
        if (!$result) return;

        $num = $result->fields[0];

        $result->Close();


    } else {
        // Get total number of links
    // TODO: make sure this is SQL standard
        //$sql = "SELECT DISTINCT COUNT(xar_iid)
        $sql = "SELECT COUNT(DISTINCT xar_iid)
                FROM $categorieslinkagetable ";
        if (isset($table) && isset($field) && isset($where)) {
            $sql .= "LEFT JOIN $table
                     ON $field = xar_iid
                     WHERE $where ";
        }

        $result = $dbconn->Execute($sql);
        if (!$result) return;

        $num = $result->fields[0];

        $result->Close();
    }

    return $num;
}
    // end of not-so-good idea

?>
