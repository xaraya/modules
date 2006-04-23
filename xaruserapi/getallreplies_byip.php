<?php
/**
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * Get a single comment or a list of comments. Depending on the parameters passed
 * you can retrieve either a single comment, a complete list of comments, a complete
 * list of comments down to a certain depth or, lastly, a specific branch of comments
 * starting from a specified root node and traversing the complete branch
 *
 * if you leave out the objectid, you -must- at least specify the author id
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @todo This is function actually returns all posters (reply authors) *not* the replies
 */

function xarbb_userapi_getallreplies_byip($args) 
{
    extract($args);

    if (!isset($modid) || empty($modid)) {
        $msg = xarML('Invalid #(1) [#(2)]', 'modid', $modid);
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return false;
    }

    // Optional argument for Pager - 
    // for those modules that use comments and require this
    if (!isset($startnum)) {
        $startnum = 1;
    } 
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $ctable = &$xartable['comments_column'];

    // initialize the commentlist array
    $commentlist = array();

    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT  $ctable[hostname] AS xar_hostname,
                    $ctable[author] AS xar_uid
              FROM  $xartable[comments]
             WHERE  $ctable[modid]= ? ";
    $bindvars = array($modid);

    if (isset($hostname) && $hostname > 0) {
        $sql .= " AND $ctable[hostname] = ? ";
        $bindvars[] = $hostname;
    }

    $sql .= " ORDER BY $ctable[left]";

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
        $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1, $bindvars);
    } else {
        $result =& $dbconn->Execute($sql, $bindvars);
    }

    //$result =& $dbconn->Execute($sql);
    if (!$result) return;

    // if we have nothing to return
    // we return nothing ;) duh? lol
    if ($result->EOF) {
        return array();
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    return $commentlist;
}

?>