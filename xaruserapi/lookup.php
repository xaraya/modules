<?php
/**
 * File: $Id:
 * 
 * Perform a passage lookup
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
 * passage lookup
 * 
 * @author curtisdf 
 * @param  $args ['sname'] short name of text to look in
 * @param  $args ['tid'] text ID to look in
 * @param  $args ['startnum'] how far down the list to start
 * @param  $args ['numitems'] verses per page
 * @param  $args ['showcontext'] whether or not to show the context of a verse
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['query'] (optional) reference to look up
 * @param  $args ['nozero']
 * @returns array
 * @return result array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_lookup($args)
{
    extract($args);

    // Set defaults
    if (empty($query)) $query = '';
    if (!isset($showcontext)) $showcontext = false;
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numitems)) $numitems = -1;
    if (!isset($rand)) $rand = false;
	if (!isset($nozero)) $nozero = false; // "zero" verse allowed except for blocks

    // Argument check
    $invalid = array();
    if (!is_numeric($startnum)) 					$invalid[] = 'startnum';
    if (!is_numeric($numitems))						$invalid[] = 'numitems';
    if (!isset($sname) && !isset($tid))				$invalid[] = 'text identifier';
    if (isset($tid) && !is_numeric($tid))			$invalid[] = 'tid';
    if (isset($objectid) && !is_numeric($objectid))	$invalid[] = 'objectid';
    if (!is_bool($rand))							$invalid[] = 'rand';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'lookup', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!empty($objectid)) $tid = $objectid;

    // get text data
    $args = array();
    if (isset($tid)) $args['tid'] = $tid;
    if (isset($sname)) $args['sname'] = $sname;
    $text = xarModAPIFunc('bible', 'user', 'get', $args);
    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return;

    // extract text ID if we don't yet have it
    if (!isset($tid)) $tid = $text['tid'];

    // get book aliases for TOC and prevnext sections
    list($aliases) = xarModAPIFunc('bible', 'user', 'getaliases',
									array('type' => 'display'));

    // security check
    if (!xarSecurityCheck('ViewBible', 1, 'Text', "$text[sname]:$tid")) return;

    // get database parameters
    list($textdbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn',
                                     array('tid' => $tid));

    // if query was empty, give table of contents
    if (empty($query)) {
        $ref = xarML('Table of Contents');

        $sqlquery = "SELECT DISTINCT xar_book, xar_chapter
                     FROM $texttable
                     GROUP BY xar_book, xar_chapter
                     ORDER BY xar_lid";

        $result = $textdbconn->Execute($sqlquery,array()); 
        if (!$result) return; 

        if ($result->EOF) return;

        $lines = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($book, $chapter) = $result->fields;

            if ($chapter == 0) continue;

            // make array with number of chapters in each book
            if (!isset($lines[$book])) $lines[$book] = array();
            $lines[$book][] = $chapter;

        }

        // translate to book display names
        $newlines = array();
        foreach ($lines as $sword => $chapters) {
            $newlines[$aliases[$sword]] = $chapters;
        }
        $lines = $newlines;

        $results = array('ref' => $ref,
                         'text' => $text,
                         'lines' => $lines);

        return $results;

    } // end if table of contents

    // get books
    list($swordbook,
         $displaybook) = xarModAPIFunc('bible', 'user', 'query2book',
                             array('sname' => $sname,
                                   'query' => $query));

    // trim off other qualifiers
    preg_match("/\s+para(llel)?=([^ \$]+)?/", $query, $match);
    if (!empty($match[0])) {
        $query = trim(str_replace($match[0], '', $query));
        if (!empty($match[2])) $snames = explode(',', $match[2]);
    } else {
        $snames = array($sname);
    }

    // split nums string into individual numbers
    $bookpart = preg_replace("/( \d{1,3}(\:\d{1,3}\w?(\-\d{1,3}\w?)?)?)?/", '', $query);
    $nums = trim(substr_replace($query.' ', '', 0, strlen($bookpart)));
    preg_match_all("/\b\d{1,3}\b/", $nums, $matches);

    // begin assembling pretty-print reference
    $ref = (!empty($matches[0]) && $displaybook == xarML('Psalms')) ? xarML('Psalm') : $displaybook;

    // prepare SQL parameters
    $bindvars = array();
    $sqlqueryhead = "SELECT *
                     FROM $texttable
                     WHERE 1\n";
    $cntqueryhead = "SELECT COUNT(1) AS count,
                     sum(char_length(concat(xar_text))) AS length
                     FROM $texttable
                     WHERE 1\n";
    $sqlquery = '';

	// handle the "zero verse"
	if ($nozero) $sqlquery .= "AND xar_verse > 0\n";

    // initialize reference placeholders
    $refbook = $refchapter = $refverse = $refverse_end = '';

    // add book condition to SQL, etc
    $sqlquery .= "AND xar_book LIKE ?\n";
    $bindvars[] = $swordbook;
    $refbook = $swordbook;

    // check if we have a chapter
    if (isset($matches[0][0])) {
        $sqlquery .= "AND xar_chapter = ?\n";
        $bindvars[] = $matches[0][0];
        $ref .= ' '.$matches[0][0];
        $refchapter = $matches[0][0];

        // check if we have a (start) verse
        if (isset($matches[0][1])) {
            $refverse = $matches[0][1];

            // check if we have an end verse
            if (isset($matches[0][2])) {
                $refverse_end = $matches[0][2];

                // make sure start verse is less than end verse
                if ($matches[0][2] < $matches[0][1]) {
                    $hold = $matches[0][1];
                    $matches[0][1] = $matches[0][2];
                    $matches[0][2] = $hold;
                }
                $sqlquery .= "AND xar_verse >= ?
                              AND xar_verse <= ?\n";
                $bindvars[] = $matches[0][1];
                $bindvars[] = $matches[0][2];
                $ref .= ':'.$matches[0][1].'-'.$matches[0][2];
            } else {
                $sqlquery .= "AND xar_verse = ?\n";
                $bindvars[] = $matches[0][1];
                $ref .= ':'.$matches[0][1];
            }
        }
    }

    $cntquery = "$cntqueryhead $sqlquery";
    $sqlquery = "$sqlqueryhead $sqlquery";

    // enable random ordering of results (for random block)
    if ($rand) $sqlquery .= "\nORDER BY RAND()";

    // get total matches
    $result = $textdbconn->Execute($cntquery,$bindvars); 
    if (!$result) return; 
    if ($result->EOF) return;

    // Length is much more relevant here than hitcount. (so why are we
    // getting a hitcount anyway? hmmm...)
    list($hitcount, $length) = $result->fields;

    // get matches (subject to startnum and numitems)
    $result = $textdbconn->SelectLimit($sqlquery, $numitems, $startnum-1, $bindvars);
    if (!$result) return; 
    if ($result->EOF) return;

    $lines = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($lid, $book, $chapter, $verse, $line, $tags) = $result->fields;

        // merge tags back in with text
        $tags = @unserialize($tags);
        foreach ($tags as $offset => $content) {
            $line = substr_replace($line, $content, $offset, 0);
        }

        $lines[] = array('lid' => $lid,
                         'book' => $book,
                         'chapter' => $chapter,
                         'verse' => $verse,
                         'text' => $line,
                         'ref' => "$book $chapter:$verse");
    }
    $result->Close(); 

    // add context if requested (but make sure we only looked up
    // one verse first)
    if ($showcontext && !empty($refverse) && empty($refverse_end)) {

        // get the lind ID we just looked up
        $context_lid = $lines[0]['lid'];

        /* Strategy: context means two verses prior and two verses after
           the verse we are currently viewing, but restricted to the
           current book  */

        // get the two prior verses
        $sqlquery = "SELECT *
                     FROM $texttable
                     WHERE xar_lid < ?
                     AND xar_book LIKE ?
                     AND xar_verse != 0
                     ORDER BY xar_lid DESC";
        $bindvars = array($context_lid, $refbook);

        // get matches (subject to startnum and numitems)
        $result = $textdbconn->SelectLimit($sqlquery, 2, 0, $bindvars);
        if (!$result) return; 

        for (; !$result->EOF; $result->MoveNext()) {
            list($lid, $book, $chapter, $verse, $line, $tags) = $result->fields;

            // merge tags back in with text
            $tags = @unserialize($tags);
            foreach ($tags as $offset => $content) {
                $line = substr_replace($line, $content, $offset, 0);
            }

            array_unshift($lines, array('lid' => $lid,
                                        'book' => $book,
                                        'chapter' => $chapter,
                                        'verse' => $verse,
                                        'text' => $line,
                                        'ref' => "$book $chapter:$verse"));
        }

        // get the two following verses
        $sqlquery = "SELECT *
                     FROM $texttable
                     WHERE xar_lid > ?
                     AND xar_book LIKE ?
                     AND xar_verse != 0
                     ORDER BY xar_lid ASC";
        $bindvars = array($context_lid, $refbook);

        // get matches (subject to startnum and numitems)
        $result = $textdbconn->SelectLimit($sqlquery, 2, 0, $bindvars);
        if (!$result) return; 

        $after = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($lid, $book, $chapter, $verse, $line, $tags) = $result->fields;

            // merge tags back in with text
            $tags = @unserialize($tags);
            foreach ($tags as $offset => $content) {
                $line = substr_replace($line, $content, $offset, 0);
            }

            array_push($lines, array('lid' => $lid,
                                     'book' => $book,
                                     'chapter' => $chapter,
                                     'verse' => $verse,
                                     'text' => $line,
                                     'ref' => "$book $chapter:$verse"));
        }

    } // end "if we need to add context"

    // assemble result parameters
    $results = array('query' => $query,
                     'text' => $text,
                     'ref' => $ref,
                     'refbook' => $refbook,
                     'refchapter' => $refchapter,
                     'refverse' => $refverse,
                     'refverse_end' => $refverse_end,
                     'showcontext' => ($showcontext) ? $context_lid : '',
                     'hitcount' => $hitcount,
                     'total_length' => $length,
                     'lines' => $lines);

    return $results;

} 

?>
