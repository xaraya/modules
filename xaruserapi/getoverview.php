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
 * @param $args['gid'] group id (future), or
 * @param $args['group'] news group
 * @param $args['startnum'] start number
 * @param $args['numitems'] number of items
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

    $messages = $newsgroups->getOverview($startnum - $numitems, $startnum);
    if (PEAR::isError($messages)) {
        $message = $messages->message;
        $newsgroups->quit();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    $newsgroups->quit();
    $messages = xarModAPIFunc('newsgroups','user','create_threads', $messages);
    $data['items'] = $messages;

    return $data;
}

?>
