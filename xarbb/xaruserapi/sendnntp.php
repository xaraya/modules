<?php
/**
 * File: $Id$
 * 
 * Update a topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.org
 *
 * @subpackage  xarbb Module
 * @author John Cox
*/
/**
 * create a new forum
 * @param $args['fname'] name of forum
 * @param $args['fdesc'] description of forum
 * @param $args['tid'] topic id to update
 * @returns int
 * @return autolink ID on success, false on failure
 */
function xarbb_userapi_sendnntp($args)
{
    // Get arguments from argument array
    extract($args);
    include_once 'modules/xarbb/xarclass/NNTP.php';
    $tpost      = wordwrap($tpost, 72, "\n", 1);
    $email      = xarUserGetVar('email');
    $name       = xarUserGetVar('name');
    $settings   = unserialize(xarModGetVar('xarbb', 'settings.'.$fid));
    $server     = $settings['nntpserver'];
    $port       = $settings['nntpport'];
    $group      = $settings['nntpgroup'];
    $addheader = "Content-Transfer-Encoding: quoted-printable\r\n".
                 "Content-Type: text/plain; charset=ISO-8859-1;\r\n".
                 "Mime-Version: 1.0\r\n".
                 'X-HTTP-Posting-Host: '.gethostbyaddr(getenv("REMOTE_ADDR"))."\r\n";

    if (!empty($reference)){
        $addheader .= "References: " . $reference . "\r\n";
    }
    $newsgroups = new Net_NNTP();
    $newsgroups -> connect($server, $port);
    $response = $newsgroups->post($ttitle, $group, $email .'('. $name .')', $tpost, $addheader);
    $newsgroups -> quit();
    return true;
}
?>