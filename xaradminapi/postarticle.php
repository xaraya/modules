<?php
/**
 * File: $Id:
 * 
 * Post article to a newsgroup
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
 * Post article to a newsgroup
 * 
 * @param $args['gid'] int group id (future), or
 * @param $args['group'] string news group
 * @param $args['subject'] string subject
 * @param $args['body'] string body
 * @param $args['name'] string name of the author (default current user)
 * @param $args['email'] string email of the author (default current user)
 * @param $args['reference'] string optional reference string
 * @param $args['host'] string optional posting host
 * @param $args['charset'] string optional character set
 * @param $args['skipencoding'] bool optional skip encoding to quoted-printable
 * @returns misc
 * @return true on success, or void on failure
 */
function newsgroups_adminapi_postarticle($args = array())
{
    if (empty($args['group'])) {
        $message = xarML('Invalid newsgroup');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    } elseif (empty($args['subject'])) {
        $message = xarML('Invalid subject');
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

    if (empty($name)) {
        $name = xarUserGetVar('name');
    }
    if (empty($email)) {
        $email = xarUserGetVar('email');
    }

    if (empty($skipencoding)) {
        // Encode the body as quoted-printable, since we are declaring it
        // as such in the header.
        $body = xarModAPIfunc('newsgroups', 'user', 'encode_quoted_printable', array('string'=>$body));
    }

    if (empty($host)) {
        $host = gethostbyaddr(xarServerGetVar('REMOTE_ADDR'));
    }

    // FIXME: adapt charset to current locale (and/or transcode) ?
    if (empty($charset)) {
        $charset = 'ISO-8859-1';
    }

    $addheader = "Content-Transfer-Encoding: quoted-printable\r\n".
                 "Content-Type: text/plain; charset=$charset;\r\n".
                 "Mime-Version: 1.0\r\n".
                 "X-HTTP-Posting-Host: $host\r\n";

    if (!empty($reference)){
        $addheader .= "References: " . $reference . "\r\n";
    }

    $from = '"' . $name . '" <' . $email . '>';

    $rs = $newsgroups->post($subject, $group, $from, $body, $addheader);
    if (PEAR::isError($rs)) {
        $message = $rs->message;
        $newsgroups->quit();
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                    new SystemException($message));
        return;
    }

    // Trick: select the group to get the last article number ?
    //$counts = $newsgroups->selectGroup($group);

    $newsgroups->quit();

/*
    // ignore errors here
    if (!empty($counts['last'])) {
        $id = $counts['last'];
        // Specify the module, itemtype and itemid so that the right hooks are called
        $args['module'] = 'newsgroups';
        $args['itemtype'] = 1; // to be replaced by group id !
        $args['itemid'] = $id;
        xarModCallHooks('item', 'create', $id, $args);

        return $id;
    }
*/

    return true;
}

?>
