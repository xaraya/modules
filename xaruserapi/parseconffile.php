<?php
/**
 * File: $Id:
 *
 * Parse a Sword .conf file, INI style
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
 * parse .conf file from Sword
 *
 * Tried using parse_ini_file() but it has limitations for special
 * characters.
 *
 * @param  $args ['file'] path to the conf file
 * @returns array
 * @return array of config parameters, with long and short names
 */
function bible_userapi_parseconffile($args)
{
    extract($args);

    $invalid = array();
    if (!isset($file) || !file_exists($file)) {
        $invalid[] = 'file';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'admin', 'parseconffile', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get contents of file
    $fd = fopen($file, 'r');
    $contents = fread($fd, filesize($file));
    fclose($fd);

    // clean up the contents
    $contents = trim($contents);
    $contents = str_replace('\\', '&#92;', $contents);

    // get md5
    $md5 = md5_file($file);

    // get short name of document
    // This takes the first "[whatever]" structure in the string
    preg_match("/\[\w+\]/", $contents, $matches);

    $sname = preg_replace("/(\[|\])/", '', $matches[0]);

    // translate contents into an array
    $config = array();
    foreach (preg_split("/(\r\n|\n\r|\r|\n)/", $contents) as $row) {

        // skip "[whatever]" line(s)
        if (empty($row) ||
            preg_match("/( |\r\n|\n\r|\r|\n)*\[\w+\]( |\r\n|\n\r|\r|\n)*/", $row)) continue;

        // split into key and variable
        preg_match_all("/([^=]{1,}) ?= ?(.*)/", $row, $matches);

        // if no "=" found, append to previous variable
        if (empty($matches[0])) {
            if (isset($key)) $config[$key] .= ' '.$row;
        } else {
            $key = strtolower($matches[1][0]);
            $value = $matches[2][0];
            $config[$key] = $value;
        }
    }

    // get long name of document
    if (isset($config['description'])) $lname = $config['description'];

    return array($sname, $lname, $md5, $config);
}

?>
