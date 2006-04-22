<?php
/**
 * Update a topic
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
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

    //$tpost      = wordwrap($tpost, 72, "\n", 1);
    $email      = xarUserGetVar('email');
    $name       = xarUserGetVar('name');
    $from       = $email .'('. $name .')';
    $settings   = unserialize(xarModGetVar('xarbb', 'settings.' . $fid));
    $server     = $settings['nntpserver'];
    $port       = $settings['nntpport'];
    $group      = $settings['nntpgroup'];

    // We should allow adding a header in the nntp module
    // $addheader = "Content-Transfer-Encoding: quoted-printable\r\n".
    //             "Content-Type: text/plain; charset=ISO-8859-1;\r\n".
    //             "Mime-Version: 1.0\r\n".
    //             'X-HTTP-Posting-Host: '.gethostbyaddr(getenv("REMOTE_ADDR"))."\r\n";

    if (empty($reference)){
        $reference = '';
    }

    if (!xarModAPIfunc('nntp', 'user', 'postarticle',
        array(
            'server'     => $server, 
            'port'       => $port, 
            'newsgroups' => $group,
            'ref'        => $reference,
            'body'       => $tpost,
            'subject'    => $ttitle,
            'from'       => $from)
        )
    ) return;

    return true;
}

?>