<?php
/**
 * Messages module
 *
 * @package modules
 * @copyright (C) 2002-2007 The copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage messages
 * @link http://xaraya.com/index.php/release/6.html
 * @author Carl P. Corliss <rabbitt@xaraya.com>
 */
/**
 * Get the number of messages sent or received by a user
 *
 * @author mikespub
 * @access public
 * @param integer    $author      the id of the author you want to count messages for, or
 * @param integer    $recipient   the id of the recipient you want to count messages for
 * @param bool       $unread      (optional) count unread rather than total
 * @param bool       $drafts      (optional) count drafts
 * @returns integer  the number of messages
 */

sys::import('modules.messages.xarincludes.defines');

function messages_userapi_get_count($args)
{
    extract($args);

    $exception = false;

    if ( (!isset($author) || empty($author)) && (!isset($recipient) || empty($recipient)) ) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                                 'author/recipient', 'userapi', 'get_count', 'messages');
        throw new BadParameterException($msg);
        $exception |= true;
    }

    if ($exception) {
        return;
    }

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    $sql = "SELECT  COUNT(id) as numitems
              FROM  $xartable[messages]
             WHERE  ";

    $bindvars = array();
    if (isset($recipient)) {
        $sql .= "recipient_delete=? AND recipient=? AND author_status!=?";
        $bindvars[] = MESSAGES_ACTIVE;
        $bindvars[] = (int) $recipient;
        $bindvars[] = MESSAGES_STATUS_DRAFT;
        if (isset($unread)) {
            $sql .= " AND recipient_status=?";
            $bindvars[] = MESSAGES_STATUS_UNREAD;
        }
    } elseif (isset($author)) {
        $sql .= " author_delete=? AND author=?";
        $bindvars[] = MESSAGES_ACTIVE;
        $bindvars[] = (int) $author;
        if (isset($unread)) {
            $sql .= " AND author_status=?";
            $bindvars[] = MESSAGES_ACTIVE;
        } elseif (isset($drafts)) {
            $sql .= " AND author_status=?";
            $bindvars[] = MESSAGES_STATUS_DRAFT;
        } else {
            $sql .= " AND author_status!=?";
            $bindvars[] = MESSAGES_STATUS_DRAFT;
        }
    }


    $result =& $dbconn->Execute($sql,$bindvars);

    if (!$result)
        return;

    if ($result->EOF) {
        return 0;
    }

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

?>
