<?php
/**
 * @package modules
 * @copyright (C) 2005-2007 The Digital Development Foundation
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
    $usesftp   = xarModGetVar('sitetools','usesftpbackup');
    // Connect and see if we use a secure connection
    if (extension_loaded('openssl') && $usesftp) {
        $conn = ftp_ssl_connect($ftpserver);
    } else {
        if ($usesftp) {
            xarLogMessage('SITETOOLS: Openssl not loaded','XARLOG_LEVEL_ERROR');
        }
        $conn = ftp_connect($ftpserver);
    }
    // Bail out if we cannot connect
    if(!$conn) {
        xarLogMessage('SITETOOLS: FTP connect failed, backup not transferred','XARLOG_LEVEL_ERROR');
       return false;
    }
    // Login
    if(!ftp_login($conn,$ftpuser,$ftppw)) {
        xarLogMessage('SITETOOLS: login failed, backup not transferred','XARLOG_LEVEL_ERROR');
        ftp_quit($conn);
        return false;
    }
    // Go to the path we want
    if(!empty($ftpdir)) {
        ftp_chdir($conn,$ftpdir);
    }

    if(!ftp_put($conn,$ftpdir.$bkname,$bkfilename,FTP_ASCII)) {
        xarLogMessage('SITETOOLS: FTP_put failed, backup not transferred','XARLOG_LEVEL_ERROR');
        return false;
    }

    ftp_quit($conn);

    // Log a message No level needed as this is debug
    xarLogMessage('SITETOOLS: Executed FTP of backup');

    return true;
}
?>