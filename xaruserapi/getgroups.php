<?php
/**
 * File: $Id:
 * 
 * Get available newsgroups from a news server
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
 * Get available newsgroups from a news server
 * 
 * @param $args['sid'] server id (future), or
 * @param $args['server'] news server name
 * @param $args['port'] news server port
 * @param $args['wildmat'] wildcard match for newsgroups
 * @param $args['user'] optional username for authentication
 * @param $args['pass'] optional password for authentication
 * @returns misc
 * @return array of newsgroups, or void on failure
 */
function newsgroups_userapi_getgroups($args)
{
    extract($args);

/* if we store server + newsgroups in a table someday
    if (!empty($sid)) {
        ... SELECT * FROM xar_newsgroups WHERE xar_sid = $sid ...
        // convert wildmat syntax to SQL syntax
        if (!empty($wildmat)) {
            $match = strtr($wildmat, array('%' => '?', '*' => '%'));
            ... AND xar_name LIKE $match ...
        }
    }
*/

// TODO: pre-load complete list of newsgroups and let admin select
//       instead of retrieving the list each time here

    if (!isset($server)) {
        $server = xarModGetVar('newsgroups', 'server');
    }
    if (!isset($port)) {
        $port = xarModGetVar('newsgroups', 'port');
    }
    if (!isset($wildmat)) {
        $wildmat = xarModGetVar('newsgroups', 'wildmat');
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

    $grouplist = array();
    if (empty($wildmat) || !strstr($wildmat,',')) {
        $grouplist = $newsgroups->getGroups(true, $wildmat);
        if (PEAR::isError($grouplist)) {
            $message = $grouplist->message;
            $newsgroups->quit();
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($message));
            return;
        }
    } else {
        $matches = explode(',',$wildmat);
        foreach ($matches as $match) {
            $groups = $newsgroups->getGroups(true, $match);
            if (PEAR::isError($groups)) {
                $message = $groups->message;
                $newsgroups->quit();
                xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                            new SystemException($message));
                return;
            }
            $grouplist = array_merge($grouplist, $groups);
        }
    }
    $newsgroups->quit();

    ksort($grouplist);

    // Return the grouplist
    return $grouplist;
}

?>
