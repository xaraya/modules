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

sys::import('modules.messages.xarincludes.defines');

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
    $sql = "SELECT  m.title AS title,
                    m.date AS datetime,
                    m.text AS text,
                    m.author AS author,
                    m.recipient AS recipient,
                    m.id AS id,
                    m.pid AS pid,
                    m.author_status AS author_status,
                    m.recipient_status AS recipient_status,
                    m.left_id AS left_id,
                    m.right_id AS right_id,
                    m.anonpost AS postanon,
                    p.anonpost AS parentanon,
                    m.author_delete AS author_delete,
                    m.recipient_delete AS recipient_delete
              FROM  $xartable[messages] m
         LEFT JOIN  $xartable[messages] as p on p.id = m.pid
             WHERE  ";

    $bindvars = array();

    if ( isset($recipient) && is_numeric($recipient)) {
        $sql .= " m.recipient=?";
        $bindvars[] = (int) $recipient;
        $sql .= " AND m.recipient_status!=?";
        $bindvars[] = MESSAGES_STATUS_DRAFT;
        if (isset($delete) && !empty($delete)) {
            $sql .= " AND m.recipient_delete=?";
            $bindvars[] = (string) $delete; 
        }
    } elseif (isset($author) && is_numeric($author)) {
        $sql .= " m.author = ?";
        $bindvars[] = (int) $author;
        if (isset($status)) {
            $sql .= " AND m.author_status=?";
            $bindvars[] = (int) $status;
        }
        if (isset($delete) && !empty($delete)) {
            $sql .= " AND m.author_delete=?";
            $bindvars[] = (string) $delete; 
        }
    }

    if ($id > 0) {
        $sql .= " AND (m.left_id >= ?";
        $sql .= " AND  m.right_id <= ?)";
        $bindvars[] = (int) $node['left_id'];
        $bindvars[] = (int) $node['right_id'];
    }

    if (!empty($orderby)) {
        $sql .= " ORDER BY m.$orderby";
    } else {
        if (!empty($reverse)) {
          $sql .= " ORDER BY m.right_id DESC";
        } else {
            $sql .= " ORDER BY m.left_id";
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
        if ($row['parentanon'] != 1) {
            $row['parentanon'] = 0;
        }
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
