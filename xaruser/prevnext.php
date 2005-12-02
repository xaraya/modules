<?php
/**
 * File: $Id:
 * 
 * Get "previous/next" pager
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
 * get "previous/next" pager
 * 
 * @author curtisdf 
 * @param  $args ['data'] results array
 * @returns array
 * @return results array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_user_prevnext($args)
{
    extract($args);

    /* TODO: add regular data validation
    if (!isset($data) || !is_array($data)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'data', 'user', 'prevnext', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    */

    // get book aliases
    list($aliases) = xarModAPIFunc('bible', 'user', 'getaliases',
                                   array('type' => 'display'));

    // get database parameters
    list($textdbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn',
                                     array('tid' => $text['tid']));

    // get query append so pager preserves parallel lookups
    array_shift($snames);
    if (empty($snames)) {
        $append = '';
    } else {
        $append = ' parallel='.join(',', $snames);
    }

    // first, get max and min lid's from reference
    $bindvars = array();
    $sqlquery = "SELECT xar_lid FROM $texttable
                 WHERE xar_book = ? ";
    $bindvars[] = $refbook;
    if (!empty($refchapter)) {
        $sqlquery .= "AND xar_chapter = ? ";
        $bindvars[] = $refchapter;
    }
    if (!empty($refverse)) {
        $sqlquery .= "AND xar_verse = ? ";
        $bindvars[] = $refverse;
    }
    $sqlquery .= "ORDER BY xar_lid ASC";

    $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
    if (!$result) return;

    list($lid_start) = $result->fields;

    $bindvars = array();
    $sqlquery = "SELECT xar_lid FROM $texttable
                 WHERE xar_book = ? ";
    $bindvars[] = $refbook;
    if (!empty($refchapter)) {
        $sqlquery .= "AND xar_chapter = ? ";
        $bindvars[] = $refchapter;
    }
    if (!empty($refverse)) {
        $sqlquery .= "AND xar_verse = ? ";
        if (!empty($refverse_end)) {
            $bindvars[] = $refverse_end;
        } else {
            $bindvars[] = $refverse;
        }
    }
    $sqlquery .= "ORDER BY xar_lid DESC";
    $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
    if (!$result) return;

    list($lid_end) = $result->fields;

    // now get prev and next links
    $prevnext = array();
    $prevnext['prevbook'][] = xarML('&lt;');
    $prevnext['prevchapter'][] = xarML('&lt;&lt;');
    $prevnext['prevverse'][] = xarML('&lt;&lt;&lt;');
    $prevnext['thisbook'][] = xarML('^');
    $prevnext['thischapter'][] = xarML('^^');
    $prevnext['nextverse'][] = xarML('&gt;&gt;&gt;');
    $prevnext['nextchapter'][] = xarML('&gt;&gt;');
    $prevnext['nextbook'][] = xarML('&gt;');

    $prevnext['prevbook'][] = xarML('Previous Book');
    $prevnext['prevchapter'][] = xarML('Previous Chapter');
    $prevnext['prevverse'][] = xarML('Previous Verse');
    $prevnext['thisbook'][] = xarML('This Book');
    $prevnext['thischapter'][] = xarML('This Chapter');
    $prevnext['nextverse'][] = xarML('Next Verse');
    $prevnext['nextchapter'][] = xarML('Next Chapter');
    $prevnext['nextbook'][] = xarML('Next Book');

    // previous book
    $sqlquery = "SELECT xar_book FROM $texttable
                 WHERE xar_lid < ?
                 AND xar_book != ?
                 AND xar_verse != 0
                 ORDER BY xar_lid DESC";
    $bindvars = array($lid_start, $refbook);

    $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
    if (!$result) return;

    list($prevbook) = $result->fields;

    if (!empty($prevbook)) {
        $prevnext['prevbook'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$prevbook].$append));
    }

    // previous chapter
    if (!empty($refchapter)) {
        $sqlquery = "SELECT xar_book, xar_chapter FROM $texttable
                     WHERE xar_lid < ?
                     AND (xar_chapter < ? OR xar_book != ?)
                     AND xar_verse != 0
                     ORDER BY xar_lid DESC";
        $bindvars = array($lid_start, $refchapter, $refbook);

        $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
        if (!$result) return;

        list($prevbook, $prevchapter) = $result->fields;

        if (!empty($prevchapter) && !empty($prevbook)) {
            $prevnext['prevchapter'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$prevbook] . ' ' . $prevchapter . $append));
        }
    }

    // previous verse
    if (!empty($refverse)) {
        $sqlquery = "SELECT xar_book, xar_chapter, xar_verse FROM $texttable
                     WHERE xar_lid < ?
                     AND (xar_verse < ? OR xar_chapter < ? OR xar_book != ?)
                     AND xar_verse != 0
                     ORDER BY xar_lid DESC";
        $bindvars = array($lid_start, $refverse, $refchapter, $refbook);

        $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
        if (!$result) return;

        list($prevbook, $prevchapter, $prevverse) = $result->fields;

        if (!empty($prevbook) && !empty($prevchapter) && !empty($prevverse)) {
            $prevnext['prevverse'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$prevbook] . ' ' . $prevchapter . ':' . $prevverse . $append));
        }
    }

    // this book
    if (!empty($refchapter)) {
        $prevnext['thisbook'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$refbook] . $append));
    }
    // this chapter
    if (!empty($refverse)) {
        $prevnext['thischapter'][] = xarModURL('bible', 'user', 'display',
                                               array('sname' => $text['sname'], 'query' => $aliases[$refbook] . ' ' . $refchapter . $append));
    }

    // next verse
    if (!empty($refverse)) {
        $sqlquery = "SELECT xar_book, xar_chapter, xar_verse FROM $texttable
                     WHERE xar_lid > ?
                     AND (xar_verse > ? OR xar_chapter > ? OR xar_book != ?)
                     AND xar_verse != 0
                     ORDER BY xar_lid ASC";
        $bindvars = array($lid_end, !empty($refverse_end) ? $refverse_end : $refverse, $refchapter, $refbook);

        $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
        if (!$result) return;

        list($nextbook, $nextchapter, $nextverse) = $result->fields;

        if (!empty($nextbook) && !empty($nextchapter) && !empty($nextverse)) {
            $prevnext['nextverse'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$nextbook] . ' ' . $nextchapter . ':' . $nextverse . $append));
        }
    }

    // next chapter
    if (!empty($refchapter)) {
        $sqlquery = "SELECT xar_book, xar_chapter FROM $texttable
                     WHERE xar_lid > ?
                     AND (xar_chapter > ? OR xar_book != ?)
                     AND xar_verse != 0
                     ORDER BY xar_lid ASC";
        $bindvars = array($lid_end, $refchapter, $refbook);

        $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
        if (!$result) return;

        list($nextbook, $nextchapter) = $result->fields;

        if (!empty($nextchapter) && !empty($nextbook)) {
            $prevnext['nextchapter'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$nextbook] . ' ' . $nextchapter . $append));
        }
    }

    // next book
    $sqlquery = "SELECT xar_book FROM $texttable
                 WHERE xar_lid > ?
                 AND xar_book != ?
                 AND xar_verse != 0
                 ORDER BY xar_lid ASC";
    $bindvars = array($lid_end, $refbook);

    $result = $textdbconn->SelectLimit($sqlquery, 1, 0, $bindvars);
    if (!$result) return;

    list($nextbook) = $result->fields;

    if (!empty($nextbook)) {
        $prevnext['nextbook'][] = xarModURL('bible', 'user', 'display',
                                            array('sname' => $text['sname'], 'query' => $aliases[$nextbook] . $append));
    }

    $data = array();
    $data['prevnext'] = $prevnext;

    return xarTplModule('bible', 'user', 'prevnext', $data);

} 

?>
