<?php
/**
 * File: $Id:
 * 
 * Get overview of messages from a newsgroup
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @author mikespub
 */
/**
 * Get overview of messages from a newsgroup
 * 
 * @param $args['gid'] int group id (future), or
 * @param $args['group'] string news group
 * @param $args['startnum'] int start number
 * @param $args['numitems'] int number of items
 * @param $args['sortby'] string optional sort field (default 'thread')
 * @param $args['order'] string optional sort order (default 'DESC')
 * @returns misc
 * @return array of counts and items, or void on failure
 */
function newsgroups_userapi_getoverview($args)
{
    extract($args);

/* if we store server + newsgroups in a table someday
    if (!empty($gid)) {
        ... SELECT * FROM xar_newsgroups WHERE xar_gid = $gid ...
    }
*/

    if (empty($group)) {
        $message = xarML('Invalid newsgroup');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }
    if (!isset($startnum)) {
        $startnum = 0;
    }
    if (!isset($numitems)) {
        $numitems = xarModGetVar('newsgroups', 'numitems');
    }

    if (!isset($server)) {
        $server = xarModGetVar('newsgroups', 'server');
    }
    if (!isset($port)) {
        $port = xarModGetVar('newsgroups', 'port');
    }
    if (!isset($user)) {
        $user = xarModGetVar('newsgroups', 'user');
    }
    if (!empty($user) && !isset($pass)) {
        $pass = xarModGetVar('newsgroups', 'pass');
    }

    include_once 'modules/newsgroups/xarclass/NNTP.php';

    $newsgroups = new Net_NNTP();
    $rs = $newsgroups->connect($server, $port);
    if (PEAR::isError($rs)) {
        $message = $rs->message;
        $newsgroups->quit();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    if (!empty($user)) {
        $rs = $newsgroups->authenticate($user,$pass);
        if (PEAR::isError($rs)) {
            $message = $rs->message;
            $newsgroups->quit();
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($message));
            return;
        }
    }

    $counts = $newsgroups->selectGroup($group);
    if (PEAR::isError($counts)) {
        $message = $counts->message;
        $newsgroups->quit();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    $data = array();
    $data['group'] = $group;
    $data['counts'] = $counts;

    if (empty($startnum)){
        $startnum = $counts['last'];
    }

    $messages = $newsgroups->getOverview($startnum - $numitems + 1, $startnum);
    if (PEAR::isError($messages)) {
        $message = $messages->message;
        $newsgroups->quit();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    $newsgroups->quit();

    if (empty($sortby) || $sortby == 'thread') {
        // this sorts by newest thread first, and then by the reference list
        $messages = xarModAPIFunc('newsgroups','user','create_threads', $messages);

    } elseif ($sortby == 'article') {
        // reverse the order of the messages (from newest to oldest)
        $messages = array_reverse($messages, true);
        // calculate the message depth based on the number of references
        foreach ($messages as $id => $message) {
            if (!empty($message['References'])) {
                $refs = explode(' ',$message['References']);
                $messages[$id]['depth'] = count($refs);
            } else {
                $messages[$id]['depth'] = 0;
            }
        }
    }

    $data['items'] = $messages;

    return $data;
}

?>
