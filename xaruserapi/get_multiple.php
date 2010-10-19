<?php
/**
 * Comments module - Allows users to post comments on items
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage comments
 * @link http://xaraya.com/index.php/release/14.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
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
 * @param integer    $modid     the id of the module that these nodes belong to
 * @param integer    $itemtype  the item type that these nodes belong to
 * @param integer    $objectid  (optional) the id of the item that these nodes belong to
 * @param integer    $id        (optional) the id of a comment
 * @param integer    $status    (optional) only pull comments with this status
 * @param integer    $author    (optional) only pull comments by this author
 * @param boolean    $reverse   (optional) reverse sort order from the database
 * @returns array     an array of comments or an empty array if no comments
 *                   found for the particular modid/objectid pair, or raise an
 *                   exception and return false.
 */
function comments_userapi_get_multiple($args)
{

    extract($args);

    if ( !isset($modid) || empty($modid) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'modid', $modid, 'userapi', 'get_multiple', 'comments');
        throw new Exception($msg);
    }

    if ( (!isset($objectid) || empty($objectid)) && !isset($author) ) {
        $msg = xarML('Invalid #(1) [#(2)] for #(3) function #(4)() in module #(5)',
                                 'objectid', $objectid, 'userapi', 'get_multiple', 'comments');
        throw new Exception($msg);
    } else
        if (!isset($objectid) && isset($author)) {
            $objectid = 0;
    }

    if (!isset($id) || !is_numeric($id)) {
        $id = 0;
    } else {
        $nodelr = xarMod::apiFunc('comments',
                                'user',
                                'get_node_lrvalues',
                                 array('id' => $id));
    }

    // Optional argument for Pager -
    // for those modules that use comments and require this
     if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($status) || !is_numeric($status)) {
        $status = _COM_STATUS_ON;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // initialize the commentlist array
    $commentlist = array();

    //Psspl:Modifided the Sql query for getting anonpost_to field.  
    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT  title AS title,
                    date AS datetime,
                    hostname AS hostname,
                    text AS text,
                    author AS author,
                    author AS role_id,
                    id AS id,
                    pid AS pid,
                    status AS status,
                    left_id AS left_id,
                    right_id AS right_id,
                    anonpost AS postanon
              FROM  $xartable[comments]
             WHERE  modid=?
               AND  status=?";
    $bindvars = array();
    $bindvars[] = (int) $modid;
    $bindvars[] = (int) $status;

    if (isset($itemtype) && is_numeric($itemtype)) {
        $sql .= " AND itemtype=?";
        $bindvars[] = (int) $itemtype;
    }

    if (isset($objectid) && !empty($objectid)) {
        $sql .= " AND objectid=?";
        $bindvars[] = (string) $objectid; // yes, this is a string in the table
    }

    if (isset($author) && $author > 0) {
        $sql .= " AND author = ?";
        $bindvars[] = (int) $author;
    }

    if ($id > 0) {
        $sql .= " AND (left_id >= ?";
        $sql .= " AND  right_id <= ?)";
        $bindvars[] = (int) $nodelr['left_id'];
        $bindvars[] = (int) $nodelr['right_id'];
    }

    if (!empty($orderby)) {
        $sql .= " ORDER BY $orderby";
    } else {
        if (!empty($reverse)) {
          $sql .= " ORDER BY right_id DESC";
        } else {
            $sql .= " ORDER BY left_id";
        }
    }
// cfr. xarcachemanager - this approach might change later
    $expire = xarModVars::get('comments','cache.userapi.get_multiple');

    //Add select limit for modules that call this function and need Pager
    if (isset($numitems) && is_numeric($numitems)) {
        if (!empty($expire)){
            $result =& $dbconn->CacheSelectLimit($expire, $sql, $numitems, $startnum-1,$bindvars);
        } else {
            $result =& $dbconn->SelectLimit($sql, $numitems, $startnum-1,$bindvars);
        }
    } else {
        if (!empty($expire)){
            $result =& $dbconn->CacheExecute($expire,$sql,$bindvars);
        } else {
            $result =& $dbconn->Execute($sql,$bindvars);
        }
    }
    if (!$result) return;

    // if we have nothing return empty
    if ($result->EOF) return array();

    if (!xarModLoad('comments','renderer')) {
        $msg = xarML('Unable to load #(1) #(2)','comments','renderer');
        throw new Exception($msg);
    }

    // zip through the list of results and
    // add it to the array we will return
    while (!$result->EOF) {
        $row = $result->GetRowAssoc(false);
        // FIXME Delete after date testing
        // $row['date'] = xarLocaleFormatDate("%B %d, %Y %I:%M %p",$row['datetime']);
        $row['date'] = $row['datetime'];
        $row['author'] = xarUserGetVar('name',$row['author']);
        comments_renderer_wrap_words($row['text'],80);
        $commentlist[] = $row;
        $result->MoveNext();
    }
    $result->Close();

    if (!comments_renderer_array_markdepths_bypid($commentlist)) {
        $msg = xarML('#(1) Unable to create depth by pid', __FILE__.'('.__LINE__.'):  ');
        throw new Exception($msg);
    }

    return $commentlist;
}

?>
