<?php
/**
 * File: $Id:
 * 
 * Get a specific text
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
 * get a specific text
 * 
 * @author curtisdf 
 * @param  $args ['tid'] id of text to get
 * @param  $args ['sname'] short name of text to get (MUST have either sname or tid)
 * @returns array
 * @return text array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_get($args)
{ 
    extract($args); 

    // Argument check
    $invalid = array();
    if (!isset($tid) && !isset($sname)) {
        $invalid[] = 'inputs';
    }
    if (isset($tid) && !is_numeric($tid)) {
        $invalid[] = 'tid';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'get', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    $dbconn = xarDBGetConn();
    $xartable = xarDBGetTables(); 

    $texttable = $xartable['bible_texts']; 

    $query = "SELECT * FROM $texttable WHERE 1 ";
    if (isset($tid)) {
        $query .= "AND xar_tid = ?";
        $bindvars = array($tid);
    } else {
        $query .= "AND xar_sname = ?";
        $bindvars = array($sname);
    }
    $result = $dbconn->Execute($query,$bindvars); 

	// quit silently if no text was found
    if (!$result) return;
    if ($result->EOF) return;

    // Obtain the item information from the result set
    list($tid, $sname, $lname, $file, $md5, $config_exists, $md5_config, $state, $type) = $result->fields; 

    $result->Close(); 

    // security check
    if (!xarSecurityCheck('ReadBible', 1, 'Text', "$sname:$tid")) {
        return;
    } 

    // Create the item array
    $text = array('tid' => $tid,
        'sname' => $sname,
        'lname' => $lname,
        'file' => $file,
        'md5' => $md5,
        'config_exists' => $config_exists,
        'md5_config' => $md5_config,
        'state' => $state,
        'type' => $type); 

    // now get config file details, if there is one
    if ($config_exists) {
        $textdir = xarModGetVar('bible', 'textdir');
        $configfile = "$textdir/".preg_replace("/\.(vpl|dat)\$/i", '.conf', $file);
        if (file_exists($configfile)) {
            list($junk, $junk, $junk,
                $config) = xarModAPIFunc('bible', 'user', 'parseconffile',
                                        array('file' => $configfile));
        } else {
            $config = array();
            $text['config_exists'] = 0;
            $text['md5_config'] = '';
        }
        $text = array_merge($config, $text);
    }

    // Return the item array
    return $text;
} 

?>
