<?php
/**
 * @package modules
 * @copyright (C) 2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sitetools
 * @link http://xaraya.com/index.php/release/887.html
 * @author Jo Dalle Nogare <jojodee@xaraya.com>
 */
/**
 * Backup tables in your database
 *
 * @author MichelV <michelv@xaraya.com>
 * @since 7 Nov 2006
 * @param array ['bkfiletype']
                 ['bkfilename']=$backupabsolutepath.$partbackupfilename;
                 ['bkname']
 * @return bool True on successful FTP action, false on failure
 * @TODO: Add in multidatabase once multidatabase functionality and location decided
 */
function sitetools_adminapi_ftpbackup($args)
{
    extract($args);
    // Security check - allow scheduler api funcs to run as anon bug #2802
    //if (!xarSecurityCheck('AdminSiteTools')) return;
    if (!extension_loaded('ftp')) {
        return false;
    }
    if(!isset($bkfilename) || empty($bkfilename)) {
        return false;
    }
    // open the connection
    $ftpserver = xarModGetVar('sitetools','ftpserver');
    $ftpuser   = xarModGetVar('sitetools','ftpuser');
    $ftppw     = xarModGetVar('sitetools','ftppw');
    $ftpdir    = xarModGetVar('sitetools','ftpdir');

    // Connect
    $conn = ftp_connect($ftpserver);
    if(!$conn) {
       return false;
    }
    // Login
    if(!ftp_login($conn,$ftpuser,$ftppw)) {
        ftp_quit($conn);
        return false;
    }
    // Go to the path we want
    ftp_chdir($conn,$ftpdir);

    if(!ftp_put($conn,$ftpdir.$bkname,$bkfilename,FTP_ASCII)) {
        return false;
    }

    ftp_quit($conn);

    // Log a message
    xarLogMessage('SITETOOLS: Excuted FTP of backup');

    return true;
}
?>