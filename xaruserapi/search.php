<?php
/*
 *
 * Keywords Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @author Alberto Cazzaniga (Janez)
*/

/**
 * 
 */
function keywords_userapi_search($args)
{
    if (!xarSecurityCheck('ReadKeywords')) return;
   
    if (empty($args) || count($args) < 1) {
        return;
    }

    extract($args);
    if($q == ''){
        return;
    }  
   
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $keywordstable = $xartable['keywords'];

    // Get item
    $query = "SELECT DISTINCT xar_id,
                   xar_keyword,
                   xar_moduleid,
                   xar_itemtype,
                   xar_itemid
                   FROM $keywordstable
                   WHERE xar_keyword LIKE '%$q%'
                   GROUP BY xar_keyword";

    $result =& $dbconn->Execute($query);
        if (!$result) return;
        
        $keys = array();
        
        if ($result->EOF) {
        $result->Close();
        return $keys;
    }

    
    for (; !$result->EOF; $result->MoveNext()) {
        list($id, $keyword, $moduleid, $itemtype, $itemid) = $result->fields;
        if (xarSecurityCheck('ReadKeywords',0)) {
            $keys[] = array('id' => $id,
                             'keyword' => $keyword,
                             'moduleid' => $moduleid,
                             'itemtype' => $itemtype,
                             'itemid' => $itemid);
        }
    }
    $result->Close();


    // Return the users
    return $keys;

}
?>
