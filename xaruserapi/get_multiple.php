<?php
/**
 * Messages module
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage messages
 * @link http://xaraya.com/index.php/release/6.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get a single message or a list of messages. Depending on the parameters passed
 * you can retrieve either a single message, a complete list of messages, a complete
 * list of messages down to a certain depth or, lastly, a specific branch of messages
 * starting from a specified root node and traversing the complete branch
 *
 * if you leave out the objectid, you -must- at least specify the author id
 *
 * @author Carl P. Corliss (aka rabbitt)
 * @access public
 * @param integer    $id        (optional) the id of a message
 * @param integer    $author    (optional) only pull messages by this author
 * @param integer    $recipient (optional) only pull messages to this recipient
 * @param integer    $status    (optional) only pull messages with this author/recipient status
 * @param boolean    $reverse   (optional) reverse sort order from the database
 * @returns array    an array of messages or an empty array if no messages
 *                   or raise an exception and return false.
 */
function messages_userapi_get_multiple($args)
{

    extract($args);

    if (!isset($id) || !is_numeric($id)) {
        $id = 0;
    } else {
        $node = xarModAPIFunc('messages',
                                'user',
                                'get',
                                 array('id' => $id));
    }

    // Optional argument for Pager -
    // for modules that use messages and require this
     if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    if (!isset($status) || !is_numeric($status)) {
        $status = 2;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    // initialize the commentlist array
    $messagelist = array();

    //Psspl:Modifided the Sql query for getting anonpost_to field.  
    // if the depth is zero then we
    // only want one comment
    $sql = "SELECT  title AS title,
                    date AS datetime,
                    text AS text,
                    author AS author,
                    author AS role_id,
                    recipient AS recipient,
                    id AS id,
                    pid AS pid,
                    author_status AS author_status,
                    recipient_status AS recipient_status,
                    left_id AS left_id,
                    right_id AS right_id,
                    anonpost AS postanon,
                    author_delete AS author_delete,
                    recipient_delete AS recipient_delete
              FROM  $xartable[messages]
             WHERE  ";

    $bindvars = array();

    if ( isset($recipient) && is_numeric($recipient)) {
        $sql .= " recipient = ?";
        $bindvars[] = (int) $author;
        if (isset($status)) {
            $sql .= " AND recipient_status=?";
            $bindvars[] = (int) $status;
        }
        if (isset($delete) && !empty($delete)) {
            $sql .= " AND (recipient_delete = 3 OR recipient_delete = ?)";
            $bindvars[] = (string) $delete; 
        }
    } elseif (isset($author) && is_numeric($author)) {
        $sql .= " recipient = ?";
        $bindvars[] = (int) $recipient;
        if (isset($status)) {
            $sql .= " AND author_status=?";
            $bindvars[] = (int) $status;
        }
        if (isset($delete) && !empty($delete)) {
            $sql .= " AND (author_delete = 3 OR author_delete = ?)";
            $bindvars[] = (string) $delete; 
        }
    }

    if ($id > 0) {
        $sql .= " AND (left_id >= ?";
        $sql .= " AND  right_id <= ?)";
        $bindvars[] = (int) $node['left_id'];
        $bindvars[] = (int) $node['right_id'];
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
    $expire = xarModVars::get('messages','cache.userapi.get_multiple');

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
        $msg = xarML('Unable to create depth by pid');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'SYSTEM_ERROR', new SystemException(__FILE__.'('.__LINE__.'):  '.$msg));
        return;
    }

    return $commentlist;
}

?>
