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
 * @param $args['startnum'] int start number (in reverse, i.e. the last article you want)
 * @param $args['numitems'] int number of items
 * @param $args['sortby'] string optional sort field (default 'thread')
 * @param $args['order'] string optional sort order (default 'DESC')
 * @returns misc
 * @return array of counts and items, or void on failure
 */
function newsgroups_userapi_getoverview($args = array())
{
    if (empty($args['group'])) {
        $message = xarML('Invalid newsgroup');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    if (!isset($args['startnum'])) {
        $args['startnum'] = 0;
    }
    if (!isset($args['numitems'])) {
        $args['numitems'] = xarModGetVar('newsgroups', 'numitems');
    }

    if (!isset($args['server'])) {
        $args['server'] = xarModGetVar('newsgroups', 'server');
    }
    if (!isset($args['port'])) {
        $args['port'] = xarModGetVar('newsgroups', 'port');
    }
    if (!isset($args['user'])) {
        $args['user'] = xarModGetVar('newsgroups', 'user');
    }
    if (!empty($args['user']) && !isset($args['pass'])) {
        $args['pass'] = xarModGetVar('newsgroups', 'pass');
    }

    extract($args);

/* if we store server + newsgroups in a table someday
    if (!empty($gid)) {
        ... SELECT * FROM xar_newsgroups WHERE xar_gid = $gid ...
    }
*/

// TODO: replace with mod or data cache ?

    $groupexpire = xarModGetVar('newsgroups','groupexpire');
    $varpath = xarCoreGetVarDirPath();
    $cachedir = realpath($varpath . '/cache');
    $cachesize = xarModGetVar('newsgroups','cachesize');
    if (!empty($cachesize) && !empty($groupexpire) &&
        !empty($cachedir) && is_dir($cachedir . '/newsgroups')) {
        if (!function_exists('xarCache_getStorage')) {
            include_once('includes/xarCache.php');
        }
        $cachestore = xarCache_getStorage(array('storage'   => 'filesystem',
                                                'type'      => 'newsgroups',
                                                'cachedir'  => $cachedir,
                                                'expire'    => $groupexpire,
                                                'sizelimit' => $cachesize,
                                                'logfile'   => ''));
        if (!empty($cachestore)) {
            // use serialized arguments as cache code
            $cachecode = md5(serialize($args));
            $cachestore->setCode($cachecode);
            $cachekey = $group;
            if ($cachestore->isCached($cachekey)) {
                $data = $cachestore->getCached($cachekey);
                if (!empty($data)) {
                    return unserialize($data);
                }
            }
        }
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

    if (!empty($cachestore) && !empty($cachekey)) {
        $cachestore->setCached($cachekey,serialize($data));
    }

    return $data;
}

?>
