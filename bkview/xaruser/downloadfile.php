<?php

/**
 * File: $Id$
 *
 * download a file directly from the repository
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @link http://www.xaraya.com
 * 
 * @subpackage bkview
 * @author Marcel van der Boom <marcel@xaraya.com>
*/

include_once("modules/bkview/xarincludes/bk.class.php");

function bkview_user_downloadfile($args) 
{
    xarVarFetch('file','str::',$filename);
    xarVarFetch('repoid','id::',$repoid);
    extract($args);

    // Get the file from the repo and sent it to the browser
    $repo_info = xarModAPIFunc('bkview','user','get',array('repoid' => $repoid));
    $repo = new bkRepo($repo_info['repopath']);
    
    // Gather info about the file and download it
    $fullname = $repo->_root . $filename;
    $basename = basename($filename);
    $size = filesize($fullname);

    // Try to determine mime-type
    $mime_type = "application/x-download";
    if(function_exists('mime_content_type')) {
        $mime_type = mime_content_type($fullname);
    } elseif ($tmp = shell_exec("file -ib $fullname")) {
        $mime_type = $tmp;
    }
    
    $fp = fopen($name, 'rb');

    // How to determine content type header?
    header("Pragma: public");
    header("Content-type: $mime_type");
    header("Content-Length: $size");
    header("Content-Disposition: attachment; filename=$basename");
    header("Accept-Ranges: bytes");
    header('Connection: close');
    fpassthru($fp);
    fclose($fp);
    exit;
}
