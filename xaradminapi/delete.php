<?php
/**
 * Delete article in a newsgroup
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2009 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage newsgroups
 * @author hb
 */
/**
 * Delete article in a newsgroup, destroy cached group overview
 *
 * NNTP Format is
 * post -> 340 Ok, ...
 * From:      <original email> of the message
 * Subject:   cmsg cancel
 * Newsgroup: as applicable
 * Control:   cancel <Message-ID>
 *
 * .
 * -> 240 Article posted ...
 *
 * @param string $args['group']     newsgroup
 * @param string $args['from']      From header or email
 * @param string $args['messageid'] message-id
 * @returns misc
 * @return true on success, or void on failure
 */
function newsgroups_adminapi_delete($args = array())
{
    if (empty($args['group'])) {
        $message = xarML('Invalid newsgroup');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }
    $args['startnum'] = 0;
    $args['numitems'] = xarModGetVar('newsgroups', 'numitems');
    $args['server'] = xarModGetVar('newsgroups', 'server');
    $args['port'] = xarModGetVar('newsgroups', 'port');
    $args['user'] = xarModGetVar('newsgroups', 'user');

    extract($args);

    if (!xarSecurityCheck('DeleteNewsGroups')) return;

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


    //Compose headers for cancel posting
    $subject = 'cmsg cancel';
    $body    = '';
    $addheader = 'Control: cancel ' . $messageid;

    $rs = $newsgroups->post($subject, $group, $from, $body, $addheader);
    if (PEAR::isError($rs)) {
        $message = $rs->message;
        $newsgroups->quit();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    $newsgroups->quit();

    //TODO Loop here to cancel more than one article, array of articlenums needed

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
            // delCached will only delete one page but needs a cachecode
            // use serialized arguments as cache code
            // $cachecode = md5(serialize($group, $startnum, $numitems, $sortby,
            //                            $server, $port, $user));
            // $cachestore->setCode($cachecode);
            $cachekey = $group;
            $cachestore->flushCached($cachekey);
        }
    }

    return true;
}

?>
