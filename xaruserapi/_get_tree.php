<?php

/**
 * get message tree
 *
 * @param $args['id'] =Integer= restrict output only to this message ID and its sibling (default none)
 * @param $args['maximum_depth'] =Integer= return messages with the given depth or less
 * @param $args['minimum_depth'] =Integer= return messages with the given depth or more
 * @param $args['indexby'] =string= specify the index type for the result array (default 'default')
 *  They only change the output IF 'id' is set:
 *    @param $args['getchildren'] =Boolean= get children of message (default false)
 *    @param $args['getparents'] =Boolean= get parents of message (default false)
 *    @param $args['return_itself'] =Boolean= return the id itself (default false)
 * @return =Array= of messages, or =Boolean= false on failure

 * Examples:
 *    get_tree() => Return all the messages
 *    get_tree(Array('id' -> ID)) => Only id and its children, grandchildren and
 *                                   every other sibling will be returned
 */
function messages_userapi_get_tree($args)
{
    extract($args);

    $dbconn = xarDB::getConn();
    $xartable = xarDB::getTables();

    if (!isset($return_itself)) {
        $return_itself = false;
    }

    if (empty($indexby)) {$indexby = 'default';}

    if (!isset($getchildren)) {
        $getchildren = false;
    }
    if (!isset($getparents)) {
        $getparents = false;
    }
    if (!isset($startnum)) {
        $startnum = 0;
    }
    elseif (!is_numeric($startnum)) {
        xarSession::setVar('errormsg', xarML('Bad numeric arguments for API function'));
        return false;
    } else {
        //The pager starts counting from 1
        //SelectLimit starts from 0
        $startnum--;
    }
    if (!isset($itemsperpage)) {
        $itemsperpage = 0;
    }
    elseif (!is_numeric($itemsperpage)) {
        xarSession::setVar('errormsg', xarML('Bad numeric arguments for API function'));
        return false;
    }

    $messagestable = $xartable['messages'];
    $bindvars = array();
    $SQLquery = "SELECT                        
                        COUNT(P2.id) AS indent,
                        id, 
                        pid,
                        date,
                        author,
                        recipient,
                        left_id,
                        right_id,
                        author_status,
                        recipient_status,
                        author_delete,
                        recipient_delete,
                        anonpost,
                        title,
                        text
                   FROM $messagestable P1,
                        $messagestable P2
                  WHERE P1.left_id
                     >= P2.left_id
                    AND P1.left_id
                     <= P2.right_id";

    if (isset($id) && !is_array($id) && $id != false)
    {
        if ($getchildren || $getparents)
        {
            // We have the message ID but we need
            // to know its left and right values
            $msg = xarModAPIFunc('messages','user','get_one',Array('id' => $id));
            if ($msg == false) {
                xarSession::setVar('errormsg', xarML('Message does not exist'));
                return Array();
            }

            // If not returning itself we need to take the appropriate
            // left values
            if ($return_itself)
            {
                $return_child_left = $msg['left_id'];
                $return_parent_left = $msg['left_id'];
            }
            else
            {
                $return_child_left = $msg['left_id'] + 1;
                $return_parent_left = $msg['left_id'] - 1;
            }

            // Introducing an AND operator in the WHERE clause
            $SQLquery .= ' AND (';
        }

        if ($getchildren)
        {
            $SQLquery .= "(P1.left_id BETWEEN ? AND ?)";
            $bindvars[] = $return_child_left; $bindvars[] = $msg['right_id'];
        }

        if ($getparents && $getchildren)
        {
               $SQLquery .= " OR ";
        }

        if ($getparents)
        {
             $SQLquery .= "( ? BETWEEN P1.left_id AND P1.right_id)";
            $bindvars[] = $return_parent_left;
        }

        if ($getchildren || $getparents)
        {
            // Closing the AND operator
            $SQLquery .= ' )';
        }
        else
        {// !(isset($getchildren)) && !(isset($getparents))
            // Return ONLY the info about the message with the given ID
            $SQLquery .= " AND (P1.id = ?) ";
            $bindvars[] = $id;
        }

    }

    // Have to specify all selected attributes in GROUP BY
    $SQLquery .= " GROUP BY P1.id, P1.pid, P1.date, P1.author, P1.recipient, P1.left_id, P1.right_id, P1.author_status, P1.recipient_status, P1.author_delete, P1.recipient_delete, P1.anonpost, P1.title, P1.text ";

    $having = array();
    // Postgre doesnt accept the output name ('indent' here) as a parameter in the where/having clauses
    // Bug #620
    if (isset($minimum_depth) && is_numeric($minimum_depth)) {
        $having[] = "COUNT(P2.id) >= ?";
        $bindvars[] = $minimum_depth;
    }
    if (isset($maximum_depth) && is_numeric($maximum_depth)) {
        $having[] = "COUNT(P2.id) < ?";
        $bindvars[] = $maximum_depth;
    }
    if (count($having) > 0) {
// TODO: make sure this is supported by all DBs we want
        $SQLquery .= " HAVING " . join(' AND ', $having);
    }

    $SQLquery .= " ORDER BY P1.left_id";

// cfr. xarcachemanager - this approach might change later
    $expire = xarModVars::get('messages','cache.userapi.get_tree');
    if (is_numeric($itemsperpage) && $itemsperpage > 0 && is_numeric($startnum) && $startnum > -1) {
        if (!empty($expire)){
            $result = $dbconn->CacheSelectLimit($expire,$SQLquery, $itemsperpage, $startnum, $bindvars);
        } else {
            $result = $dbconn->SelectLimit($SQLquery, $itemsperpage, $startnum, $bindvars);
        }
    } else {
        if (!empty($expire)){
            $result = $dbconn->CacheExecute($expire,$SQLquery,$bindvars);
        } else {
            $result = $dbconn->Execute($SQLquery, $bindvars);
        }
    }

    if (!$result) return;

    if ($result->EOF) {
        //It´s ok.. no message found
        // The user doesn´t need to be informed, he will see it....
//        xarSession::setVar('statusmsg', xarML('No message found'));
        return Array();
    }

    $messages = array();

    $index = -1;
    while (!$result->EOF) {
        list($indentation,
                $id,
                $pid,
                $date,
                $author,
                $recipient,
                $left_id,
                $right_id,
                $author_status,
                $recipient_status,
                $author_delete,
                $recipient_delete,
                $anonpost,
                $title,
                $text
               ) = $result->fields;
        $result->MoveNext();

        // need workaround for colons in secchecks ($title like 'RE: ...'
        if (!xarSecurityCheck('ViewMessages',0,'Message',"$title:$id")) {
             continue;
        }

        if ($indexby == 'id') {
            $index = $id;
        } else {
            $index++;
        }

        // are we looking to have the output in the "standard" form?
        if (!empty($dropdown)) {
            $messages[$index+1] = Array(
                'id'         => $id,
                'title'        => $title,
            );
        } else {
            $messages[$index] = Array(
                'indentation' => $indentation,
                'id'               => $id,
                'pid'              => $pid,
                'date'             => $date,
                'author'           => $author,
                'recipient'        => $recipient,
                'left_id'          => $left_id,
                'right_id'         => $right_id,
                'author_status'    => $author_status,
                'recipient_status' => $recipient_status,
                'author_delete'    => $author_delete,
                'recipient_delete' => $recipient_delete,
                'anonpost'         => $anonpost,
                'title'            => $title,
                'text'             => $text
            );
        }
    }
    $result->Close();

    if (!empty($dropdown)) {
        $messages[0] = array('id' => 0, 'title' => '');
    }

    return $messages;
}

?>
