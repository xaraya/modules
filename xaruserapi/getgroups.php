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
 * @param $args['sid'] int server id (future), or
 * @param $args['server'] string news server name
 * @param $args['port'] int news server port
 * @param $args['wildmat'] string wildcard match for newsgroups
 * @param $args['user'] string optional username for authentication
 * @param $args['pass'] string optional password for authentication
 * @param $args['nocache'] bool optional flag to skip cached info
 * @returns misc
 * @return array of newsgroups, or void on failure
 */
function newsgroups_userapi_getgroups($args = array())
{
    if (empty($args['nocache'])) {
        $grouplist = xarModGetVar('newsgroups','grouplist');
        if (!empty($grouplist)) {
            return unserialize($grouplist);
        }
    }

    if (!isset($args['server'])) {
        $args['server'] = xarModGetVar('newsgroups', 'server');
    }
    if (!isset($args['port'])) {
        $args['port'] = xarModGetVar('newsgroups', 'port');
    }
    if (!isset($args['wildmat'])) {
        $args['wildmat'] = xarModGetVar('newsgroups', 'wildmat');
    }
    if (!isset($args['user'])) {
        $args['user'] = xarModGetVar('newsgroups', 'user');
    }
    if (!empty($args['user']) && !isset($args['pass'])) {
        $args['pass'] = xarModGetVar('newsgroups', 'pass');
    }

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

// TODO: replace with mod or data cache ?

    $listexpire = xarModGetVar('newsgroups','listexpire');
    $varpath = xarCoreGetVarDirPath();
    $cachedir = realpath($varpath . '/cache');
    $cachesize = xarModGetVar('newsgroups','cachesize');
    if (!empty($cachesize) && !empty($listexpire) &&
        !empty($cachedir) && is_dir($cachedir . '/newsgroups')) {
        if (!function_exists('xarCache_getStorage')) {
            include_once('includes/xarCache.php');
        }
        $cachestore = xarCache_getStorage(array('storage'   => 'filesystem',
                                                'type'      => 'newsgroups',
                                                'cachedir'  => $cachedir,
                                                'expire'    => $listexpire,
                                                'sizelimit' => $cachesize,
                                                'logfile'   => ''));
        if (!empty($cachestore)) {
            // use serialized arguments as cache code
            $cachecode = md5(serialize($args));
            $cachestore->setCode($cachecode);
            $cachekey = 'grouplist';
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

    if (!empty($cachestore) && !empty($cachekey)) {
        $cachestore->setCached($cachekey,serialize($grouplist));
    }

    // Return the grouplist
    return $grouplist;
}

?>
