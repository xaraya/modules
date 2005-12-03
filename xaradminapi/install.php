<?php
/**
 * File: $Id:
 *
 * Install a text
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
 * install a text
 *
 * @author curtisdf
 * @param  $args ['tid'] ID of the text
 * @returns bool
 * @return true on success, false on failure
 * @raise BAD_PARAM, NO_PERMISSION, DATABASE_ERROR
 */
function bible_adminapi_install($args)
{
    extract($args);

    if (!isset($tid) || !is_numeric($tid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'item ID', 'admin', 'install', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get text data
    $text = xarModAPIFunc('bible', 'user', 'get',
                          array('tid' => $tid, 'state' => 'all'));

    // make sure file matches what's in the record
    $textdir = xarModGetVar('bible', 'textdir');
    if (!file_exists("$textdir/$text[file]") ||
        md5_file("$textdir/$text[file]") != $text['md5']) {
        $msg = xarML('File #(1) does not exist or has changed since record was last updated',
            $text['file']);
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // Check for exceptions
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // security check
    if (!xarSecurityCheck('AddBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    }

    // get database and table parameters
    list($dbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn', array('tid' => $tid));

    xarDBLoadTableMaintenanceAPI();

    // if table already exists, remove it so we can re-create it
    $query = "DROP TABLE IF EXISTS $texttable";
    $dbconn->Execute($query);
    if ($dbconn->ErrorNo()) return;

    // generate table definition array
    if ($text['type'] == 1) {
        // type 1 is Bible
        $fields = array('xar_lid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
            'xar_book' => array('type' => 'varchar', 'size' => '32', 'null' => false),
            'xar_chapter' => array('type' => 'integer', 'size' => 'small', 'null' => false),
            'xar_verse' => array('type' => 'integer', 'size' => 'small', 'null' => false),
            'xar_text' => array('type' => 'text', 'null' => false),
            'xar_tags' => array('type' => 'text', 'null' => false));

    } elseif ($text['type'] == 2) {
        // type 2 is Strong's Concordance
        $fields = array('xar_wid' => array('type' => 'integer', 'null' => false, 'increment' => true, 'primary_key' => true),
            'xar_num' => array('type' => 'integer', 'size' => 'small', 'null' => false),
            'xar_word' => array('type' => 'varchar', 'size' => '32', 'null' => false),
            'xar_pron' => array('type' => 'varchar', 'size' => '32', 'null' => false),
            'xar_def' => array('type' => 'text', 'null' => false));

    } else {
        // only recognize type 1 and 2, so all others we return
        return;
    }

    // create the table
    $query = xarDBCreateTable($texttable, $fields);
    if (empty($query)) return; // throw back
    $result = $dbconn->Execute($query);
    if (!$result) return;

    // load file into database
    $fd = fopen("$textdir/$text[file]", 'r');

    // how we add depends on text type
    if ($text['type'] == 1) {

        // begin our SQL query here, and add to it as we loop
        $query_start = "INSERT INTO $texttable (
                        xar_lid,
                        xar_book,
                        xar_chapter,
                        xar_verse,
                        xar_text,
                        xar_tags)
                        VALUES ";

        $count = 0;
        $queries = $bindvars = array();
        $trans = array("\x91" => '\'',
                        "\x92" => '\'',
                        "\x93" => '"',
                        "\x94" => '"',
                        "\x96" => '--',
                        "\x97" => '--');
        while (!feof($fd)) {

            // read the next line from the file
            $buffer = trim(fgets($fd));

            // skip to next line if empty
            if (empty($buffer)) continue;

            // separate reference from text
            preg_match("/^([^\:]+) (\d{1,3})\:(\d{1,3}\w?) (.*)/", $buffer, $matches);
            @list($string, $book, $chapter, $verse, $line) = $matches;

            // separate tags from text
            preg_match_all("/<[^>]*>/", $line, $matches, PREG_OFFSET_CAPTURE);

            $tags = array();
            foreach ($matches[0] as $row) {
                $tags[$row[1]] = $row[0];
            }

            // translate fancy quote marks to HTML-sane chars
            $line = strtr(strip_tags($line), $trans);

            $queries[] = "('', ?, ?, ?, ?, ?)";
            $bindvars[] = $book;
            $bindvars[] = $chapter;
            $bindvars[] = $verse;
            $bindvars[] = $line;
            $bindvars[] = serialize($tags);

            // insert into DB several verses at a time
            $count++;
            if ($count > 100) {

                // insert this set into the database
                $query = $query_start . join(', ', $queries);
                $result = $dbconn->Execute($query,$bindvars);
                if (!$result) return;

                // reset loop parameters
                $count = 0;
                $queries = $bindvars = array();
            }

        }

        // insert last set into the database
        $query = $query_start . join(', ', $queries);
        $result = $dbconn->Execute($query,$bindvars);

        if (!$result) return;

        // generate SQL that will create the index
        $query = "ALTER TABLE $texttable
                    ADD FULLTEXT xar_index (xar_text)";
        $dbconn->Execute($query);

        if ($dbconn->ErrorNo()) return;

    } elseif ($text['type'] == 2) {

        // begin our SQL query here, and add to it as we loop
        $query_start = "INSERT INTO $texttable (
                        xar_wid,
                        xar_num,
                        xar_word,
                        xar_pron,
                        xar_def)
                        VALUES ";

        // scroll through until we get to the first definition
        while (!feof($fd)) {
            $buffer = fgets($fd);
            if (preg_match("/\\$\\$\w\d+\s*\$/", $buffer)) break;
        }

        $count = 0;
        $queries = $bindvars = array();
        while (!feof($fd)) {

            // read the next line from the file
            $buffer = fgets($fd);

            // if we match the '$$T0000000' lines...
            if (preg_match("/^\s*\\$\\$\w\d+\s*\$/", $buffer)) {

            // save previous def
                $queries[] = "('', ?, ?, ?, ?)";
                $bindvars[] = $num;
                $bindvars[] = $word;
                $bindvars[] = $pron;
                $bindvars[] = trim($def);

                // insert into DB several verses at a time
                $count++;
                if ($count > 200) {

                    // insert this set into the database
                    $query = $query_start . join(', ', $queries);
                    $result = $dbconn->Execute($query,$bindvars);
                    if (!$result) return;

                    // reset loop parameters
                    $count = 0;
                    $queries = $bindvars = array();
                }

            // if we match the '\00000\' lines...
            } elseif (preg_match("/^\s*\\\(\d+)\\\s*\$/", $buffer, $match)) {
                continue;

            // if we match '0000  word  pronunciation' lines...
            } elseif (preg_match("/^\s*(\d+)\s+([^ ]+)\s+([^ ]+)\s*\$/", $buffer, $match)) {

                // start over with new definition
                $num = $match[1];
                $word = $match[2];
                $pron = $match[3];
                $def = '';

            // if we match anything else...
            } else {
                $def .= $buffer;
            }

        }

        // insert last set into the database
        $queries[] = "('', ?, ?, ?, ?)";
        $bindvars[] = $num;
        $bindvars[] = $word;
        $bindvars[] = $pron;
        $bindvars[] = trim($def);
        $query = $query_start . join(', ', $queries);
        $result = $dbconn->Execute($query,$bindvars);

        if (!$result) return;

    }
    fclose($fd);

    // Let the calling process know that we have finished successfully
    return true;
}

?>