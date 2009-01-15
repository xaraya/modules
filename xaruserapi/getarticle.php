<?php
/**
 * File: $Id:
 * 
 * Get article from a newsgroup
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 - 2009 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @author mikespub
 */
/**
 * Get article from a newsgroup
 * 
 * @param $args['gid'] int group id (future), or
 * @param $args['group'] string news group
 * @param $args['article'] int article, or
 * @param $args['messageid'] string message id
 * @param $args['getrefnum'] bool (try to) get the reference numbers
 * @returns misc
 * @return array of counts, headers and body, or void on failure
 */
function newsgroups_userapi_getarticle($args = array())
{
    if (empty($args['group'])) {
        $message = xarML('Invalid newsgroup');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    } elseif (empty($args['article']) && empty($args['messageid'])) {
        $message = xarML('Invalid article');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
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

    $messageexpire = xarModGetVar('newsgroups','messageexpire');
    $varpath = xarCoreGetVarDirPath();
    $cachedir = realpath($varpath . '/cache');
    $cachesize = xarModGetVar('newsgroups','cachesize');
    if (!empty($cachesize) && !empty($messageexpire) &&
        !empty($cachedir) && is_dir($cachedir . '/newsgroups')) {
        if (!function_exists('xarCache_getStorage')) {
            include_once('includes/xarCache.php');
        }
        $cachestore = xarCache_getStorage(array('storage'   => 'filesystem',
                                                'type'      => 'newsgroups',
                                                'cachedir'  => $cachedir,
                                                'expire'    => $messageexpire,
                                                'sizelimit' => $cachesize,
                                                'logfile'   => ''));
        if (!empty($cachestore)) {
            // use serialized arguments as cache code
            $cachecode = md5(serialize($args));
            $cachestore->setCode($cachecode);
            if (!empty($article)) {
                $cachekey = $group . '-' . $article;
            } else {
                $cachekey = $group . '-' . md5($messageid);
            }
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

    if (empty($article) && !empty($messageid)) {
        $headers = $newsgroups->splitHeaders($messageid);
        if (PEAR::isError($headers)) {
            $message = $headers->message;
            $newsgroups->quit();
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($message));
            return;
        }
        if (!empty($headers['Xref']) && preg_match("/ $group:(\d+)/",$headers['Xref'],$matches)) {
            $article = $matches[1];
        } else {
            $article = $messageid;
        }
    } else {
        $headers = $newsgroups->splitHeaders($article);
        if (PEAR::isError($headers)) {
            $message = $headers->message;
            $newsgroups->quit();
            xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                        new SystemException($message));
            return;
        }
    }
    $data['article'] = $article;

    if (!empty($headers['References'])) {
        // join multi-line headers together
        if (is_array($headers['References'])) {
            $headers['References'] = join(' ',$headers['References']);
        }
        // split for each individual reference
        $headers['References'] = preg_split('/\s*(?=<)/',$headers['References'],-1,PREG_SPLIT_NO_EMPTY);
    }

    // translate References to article numbers
    if (!empty($getrefnum) && !empty($headers['References']) && is_array($headers['References'])) {
        $numrefs = count($headers['References']);
        for ($i = 0; $i < $numrefs; $i++) {
            $ref = str_replace(array('&lt;','&gt;'),
                               array('<','>'),
                               $headers['References'][$i]);
            // STAT doesn't seem to work with message id's, and XHDR may not be available
            $stat = $newsgroups->splitHeaders($ref);
            if (PEAR::isError($stat)) {
                // Skip missing articles
                continue;
            }
            
            if (!empty($stat['Xref']) && is_array($stat['Xref'])) {
                // get the last reference if necessary
                $stat['Xref'] = array_pop($stat['Xref']);
            }
            if (!empty($stat['Xref']) && preg_match("/ $group:(\d+)/",$stat['Xref'],$matches)) {
                $headers['References'][$i] = $matches[1];
            }
        }
    }
    $data['headers'] = $headers;

    $data['body'] = $newsgroups->getBody($article);

    $newsgroups->quit();

    if (!empty($cachestore) && !empty($cachekey)) {
        $cachestore->setCached($cachekey,serialize($data));
    }

    return $data;
}

?>
