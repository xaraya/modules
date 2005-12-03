<?php
/**
 * File: $Id:
 *
 * Scan Bible text directory and update list of texts
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage bible
 * @author curtisdf
 */
/**
 * scan directory and update text states
 *
 * @author curtisdf
 * @returns array
 * @return list of texts
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_scantextdir($args)
{
    // security check
    if (!xarSecurityCheck('EditBible', 1)) return;

    extract($args);

    // get module vars
    $textdir = xarModGetVar('bible', 'textdir');

    // set status and redirect to modifyconfig if text dir not available
    if (!is_readable($textdir)) {
        xarSessionSetVar('statusmsg', xarML('Text directory does not exist or is not readable by web server.  Please set it before continuing.'));
        xarResponseRedirect(xarModURL('bible', 'admin', 'modifyconfig'));
        return true;
    }

    // get texts in file system
    $filetexts = array();
    $hd = opendir($textdir);
    while (false !== ($file = readdir($hd))) {
        // only do VPLs and DATs at this point
        if (preg_match("/\.(vpl|dat)\$/i", $file)) {
            $filetexts[$file] = md5_file("$textdir/$file");
        }
    }

    // get texts in database
    $dbtexts = xarModAPIFunc('bible', 'user', 'getall', array('state' => 'all'));

    // check for differences between file and db texts
    $newtexts = $filetexts;
    foreach ($dbtexts as $text) {
        $filetext = "$textdir/$text[file]";

        if (isset($newtexts[$text['file']])) {
            unset($newtexts[$text['file']]);
        }

        // handle updated and missing files
        $configfile = preg_replace("/\.(vpl|dat)\$/i", '.conf', $filetext);
        if ((file_exists($filetext) && $text['md5'] != $filetexts[$text['file']]) || // text file changed
            (file_exists($configfile) && $text['config_exists'] == false) || // config just added
            (file_exists($configfile) && $text['md5_config'] != md5_file($configfile)) // config file changed
           ) {

            /* file is updated */

            // set state to 3 (updated)
            if (!xarModAPIFunc('bible', 'admin', 'setstate',
                               array('tid' => $text['tid'], 'newstate' => 3))) {
                return;
            }

            $text['state'] = 3;
        } else if (($text['state'] == 0 && !file_exists($filetext)) || // text file missing
                   ($text['config_exists'] && !file_exists($configfile)) // config file missing
                  ) {

            /* file is missing and state is new/uninstalled */

            // set state to 4 (missing)
            if (!xarModAPIFunc('bible', 'admin', 'setstate',
                                array('tid' => $text['tid'], 'newstate' => 4))) {
                return;
            }
            $text['state'] = 4;

        }

    }

    // prepare database
    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables();

    $texttable = $xartable['bible_texts'];

    // add new texts to database
    foreach ($newtexts as $file => $md5) {

        // get remaining vars
        $tid = $dbconn->GenId($texttable);

        // get document type
        if (preg_match("/\.vpl\$/i", $file)) {
            $type = 1;
        } elseif (preg_match("/\.dat\$/", $file)) {
            $type = 2;
        } else {
            $type = 0;
        }

        // see if config file exists
        $configfile = "$textdir/".preg_replace("/\.(vpl|dat)\$/i", '.conf', $file);

        if (file_exists($configfile)) {
            $config_exists = true;
            list($sname,
                 $lname,
                 $md5_config,
                 $config) = xarModAPIFunc('bible', 'user', 'parseconffile',
                                          array('file' => $configfile));
        } else {
            $config_exists = false;
            $md5_config = '';
            $sname = strtoupper(preg_replace("/\.(vpl|dat)\$/i", '', $file));
            $lname = $sname;
        }

        // make SQL to add item
        $query = "INSERT INTO $texttable (
                    xar_tid,
                    xar_sname,
                    xar_lname,
                    xar_file,
                    xar_md5,
                    xar_config_exists,
                    xar_md5_config,
                    xar_state,
                    xar_type)
                  VALUES (?,?,?,?,?,?,?,0,?)";

        // translate config_exists boolean to integer, so SQL doesn't throw error
        if (!$config_exists) $config_exists = '0';

        $bindvars = array($tid, $sname, $lname, $file, $md5, $config_exists, $md5_config, $type);
        $result = $dbconn->Execute($query, $bindvars);

        if (!$result) return;

        $tid = $dbconn->PO_Insert_ID($texttable, 'xar_tid');

        $dbtexts[$tid] = array('tid' => $tid,
                             'sname' => $sname,
                            'lname' => '',
                            'file' => $file,
                            'md5' => $md5,
                            'state' => 0,
                            'type' => $type);

    }

    // Return the id of the newly created item to the calling process
    return $dbtexts;
}

?>
