<?php

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
    if (count($cids) > 0) {
        // We do.  We just need to know how many articles there are in these
        // categories
        // Get number of links with those categories in cids
        // TODO: make sure this is SQL standard
        //$sql = "SELECT DISTINCT COUNT(xar_iid)
        $sql = "SELECT COUNT(DISTINCT xar_iid)
                FROM $categorieslinkagetable ";
        if (isset($table) && isset($field) && isset($where)) {
            $sql .= "LEFT JOIN $table ON $field = xar_iid;";
        }
        $sql .= "  WHERE ";
        
        $allcids = join(', ', $cids);
        $sql .= "xar_cid IN (" . xarVarPrepForStore($allcids) . ") ";

        if (isset($table) && isset($field) && isset($where)) {
            $sql .= " AND $where ";
        }

        $result = $dbconn->Execute($sql);
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
