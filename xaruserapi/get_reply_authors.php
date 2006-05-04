<?php

/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author Jason Judge
*/
/**
 * Get a list of authors who have replied to topics.
 * Replaces 'getallreplies_byip'.
 *
 * @author Jason Judge
 * @access public
 * @param ip string The IP address the reply/comment was made from
 * @todo: we may want to know authors having replied to specific forums, and perhaps order by rank.
 */

function xarbb_userapi_get_reply_authors($args) 
{
    extract($args);

    $modid = xarModGetIDFromName('xarbb');

    // Optional argument for Pager - 
    // for those modules that use comments and require this
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numitems)) $numitems = -1;

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT c_tab.xar_hostname, c_tab.xar_author, COUNT(c_tab.xar_author) AS c_tab_uid_count"
        . " FROM $xartable[comments] AS c_tab"
        . " WHERE c_tab.xar_modid = ?";
    $bindvars = array($modid);

    if (isset($ip) && $ip > 0) {
        $sql .= " AND c_tab.xar_hostname = ? ";
        $bindvars[] = (string)$ip;
    }

    $sql .= " GROUP BY c_tab.xar_hostname, c_tab.xar_author ORDER BY c_tab_uid_count";

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    } else {
        $result =& $dbconn->Execute($sql, $bindvars);
    }

    //$result =& $dbconn->Execute($sql);
    if (!$result) return;

    // initialize the commentlist array
    $commentlist = array();

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        list($hostname, $author, $author_count) = $result->fields;
        $commentlist[] = array(
            'ip' => $hostname,
            'uid' => $author,
            'uid_count' => $author_count,
        );
        $result->MoveNext();
    }
    $result->Close();

    return $commentlist;
}

?>