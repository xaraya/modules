<?php
/**
 * File: $Id:
 * 
 * Perform keyword search
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
 * perform keyword search
 * 
 * @author curtisdf 
 * @param  $args ['sname'] short name of text to look in
 * @param  $args ['tid'] text ID to look in
 * @param  $args ['searchtype'] type of full-text search (normal|boolean|expansion|fuzzy)
 * @param  $args ['startnum'] how far down the list to start
 * @param  $args ['numitems'] verses per page
 * @param  $args ['objectid'] a generic object id (if called by other modules)
 * @param  $args ['query'] keywords to look up
 * @returns array
 * @return result array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_search($args)
{
    extract($args);

	# set defaults
    if (!isset($startnum)) $startnum = 1;
    if (!isset($numitems)) $numitems = -1;
    if (!isset($rand)) $rand = false;
    if (!isset($limits)) $limits = array();
    if (!isset($text)) $text = array();

    // Argument check
    $invalid = array();
    if (!isset($query))								$invalid[] = 'query';
    if (!is_numeric($startnum))						$invalid[] = 'startnum';
    if (!is_numeric($numitems))						$invalid[] = 'numitems';
    if (!is_array($limits))							$invalid[] = 'limits';
    if (!isset($sname) && !isset($tid))				$invalid[] = 'text identifier';
    if (isset($tid) && !is_numeric($tid))			$invalid[] = 'tid';
    if (isset($objectid) && !is_numeric($objectid))	$invalid[] = 'objectid';
    if (!is_bool($rand))							$invalid[] = 'rand';
    if (!is_array($text))							$invalid[] = 'text';

    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'lookup', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    if (!empty($objectid)) $tid = $objectid;

    // get text data if we weren't given it already
	if (empty($text)) {
	    $args = array();
	    if (isset($tid)) $args['tid'] = $tid;
	    if (isset($sname)) $args['sname'] = $sname;

	    $text = xarModAPIFunc('bible', 'user', 'get', $args);
	    if (!isset($text) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
	}

    // create results container
    $results = array();
    $results['text'] = $text;
    $results['query'] = $query;

    // extract text ID if we don't yet have it
    if (!isset($tid)) $tid = $text['tid'];

    // security check
    if (!xarSecurityCheck('ViewBible', 1, 'Text', "$text[sname]:$tid")) {
        return;
    } 

    // get books
    list($aliases) = xarModAPIFunc('bible', 'user', 'getaliases',
                                   array('type' => 'display'));

    // get database parameters
    list($textdbconn,
         $texttable) = xarModAPIFunc('bible', 'user', 'getdbconn',
                                     array('tid' => $tid));

    // ref in this case is the title at the top of the search results list
    $ref = $query;

    // translate any HTML entities back to their special characters
    $trans_table = array_flip(get_html_translation_table(HTML_ENTITIES));
    $query = strtr($query, $trans_table);

    /**
	 * figure out which searchtype to use
	 */
	// did the user specify already?
    if (preg_match("/^(normal|expansion|fuzzy|boolean)\: /", $query, $match)) {
        $searchtype = $match[1];
        $query = preg_replace("/^\w+\: /", '', $query);

	// otherwise we depend on presence of MySQL fulltext+boolean operators
	} else {
        // note: normal mode ignores phrases, so send it to boolean
        $test = $query;
        $test = preg_replace("/(^|\s+)sort=([^\b]*)\s*/", '', $test);
        $test = preg_replace("/\w+([^\w ]\w+)?/", '', $test);
        $test = trim($test);

        $searchtype = empty($test) ? 'normal' : 'boolean';
    }

    // make limits string
    $sqllimits = '';
    if (!empty($limits)) {
        $sqlqueries = array();
        foreach ($limits as $limitrow) {
            if (is_numeric($limitrow[0])) {
                foreach ($limitrow as $lid) {
                    $sqlqueries[] = "xar_lid = $lid\n";
                }
            } else {
                foreach ($limitrow as $swordbook) {
                    $sqlqueries[] = "xar_book LIKE '".addslashes($swordbook) . "'\n";
                }
            }
        }
        if (!empty($sqlqueries)) $sqllimits = ' AND (' . join(' OR ', $sqlqueries) . ') ';
    }

    switch($searchtype) {
    case 'expansion': case 'fuzzy':

        // the 'sort=foo' term doesn't apply here due to the nature of
        // query expansion searches -- we always want sorting by score
        preg_match("/(^|\s+)sort=([^\b]*)\s*/", $query, $match);
        if (!empty($match[0])) $query = str_replace($match[0], '', $query);

        /* Backport MySQL's 'WITH QUERY EXPANSION' feature.
           (Otherwise we'd have to have MySQL 4.1.1 or more.)

           We grab the text of the first few matches, then append them
            to the search terms and search again.                         */

        // get how many hits we would get with just the bare query
        $sqlquery = "SELECT COUNT(1)
                    FROM $texttable
                    WHERE MATCH (xar_text) AGAINST (?)
                     AND xar_verse != 0
                     $sqllimits";
        $bindvars = array($query);

        $result = $textdbconn->Execute($sqlquery, $bindvars);
        if (!$result) return; 

        list($hitcount) = $result->fields;

        // make the number of items we combine with the search terms a
        // percentage of the hits
        $percent = 5;
        $expitems = round($hitcount * $percent/100);
        if ($expitems == 0) $expitems = 1;

        // get a few of the top hits so we can use them in the second query
        $sqlquery = "SELECT xar_text
                     FROM $texttable
                     WHERE MATCH (xar_text) AGAINST (?)
                     AND xar_verse != 0
                     $sqllimits";
        $bindvars = array($query);

        $result = $textdbconn->SelectLimit($sqlquery, $expitems, 1, $bindvars); 
        if (!$result) return; 
        if ($result->EOF) return;

        // tack on unique 4+ letter words from top subsection of hits, and search again
        $words = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($text) = $result->fields;
            preg_match_all("/(\w+([^\w ]\w+)*){4,}/", $text, $matches);
            $words = array_merge($matches[0], $words);
        }
        $words = array_unique($words);
        $newquery = $query . ' ' . join(' ', $words);

        // get total count of hits
        $sqlquery = "SELECT COUNT(1)
                    FROM $texttable
                    WHERE MATCH (xar_text) AGAINST (?)
                     AND xar_verse != 0
                     $sqllimits";
        $bindvars = array($newquery);

        $result = $textdbconn->Execute($sqlquery, $bindvars);
        if (!$result) return; 

        list($hitcount) = $result->fields;
/*
        // get list of lid's for potential search limits
        $sqlquery = "SELECT xar_lid FROM $texttable
                    WHERE MATCH (xar_text) AGAINST (?)
                     AND xar_verse != 0
                     $sqllimits";
        $bindvars = array($newquery);

        $result = $textdbconn->Execute($sqlquery, $bindvars);
        if (!$result) return; 

        $lids = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($lid) = $result->fields;
            $lids[] = $lid;
        }
        $nextlimits = join(',', $lids);
*/
$nextlimits = '';

        // get a portion of the hits
        $sqlquery = "SELECT *,
                     MATCH (xar_text) AGAINST (?) AS score
                    FROM $texttable
                    WHERE MATCH (xar_text) AGAINST (?)
                     AND xar_verse != 0
                     $sqllimits
                    ORDER BY score DESC";
        $bindvars = array($newquery, $newquery);

        $result = $textdbconn->SelectLimit($sqlquery, $numitems, $startnum-1, $bindvars); 
        if (!$result) return; 
        if ($result->EOF) return;

        break;
    case 'boolean':

        $boolean = ' IN BOOLEAN MODE';

        // don't break here
    case 'normal': default:

        if (!isset($boolean)) $boolean = '';

        // get sorting preferences
        preg_match("/(^|\s+)sort=([^\b]*)?\s*/", $query, $match);
        $sort = '';
        if (!empty($match[0])) $query = str_replace($match[0], '', $query);
        if (!empty($match[2])) $sort = $match[2];

        // get total count of hits
        $sqlquery = "SELECT COUNT(1)
                    FROM $texttable
                    WHERE MATCH (xar_text) AGAINST (? $boolean) > 0
                     AND xar_verse != 0
                     $sqllimits";
        $bindvars = array($query);

        $result = $textdbconn->Execute($sqlquery, $bindvars);

        if (!$result) return; 

        list($hitcount) = $result->fields;
/*
        // get list of lid's for potential search limits
        $sqlquery = "SELECT xar_lid FROM $texttable
                     WHERE MATCH (xar_text) AGAINST (? $boolean) > 0
                     AND xar_verse != 0
                     $sqllimits";
        $bindvars = array($query);

        $result = $textdbconn->Execute($sqlquery, $bindvars);
        if (!$result) return; 

        $lids = array();
        for (; !$result->EOF; $result->MoveNext()) {
            list($lid) = $result->fields;
            $lids[] = $lid;
        }
        $nextlimits = join(',', $lids);
*/
$nextlimits = '';

        // get a portion of the hits according to numitems and startnum
        $bindvars = array();
        $sqlquery = "SELECT *,
                     MATCH (xar_text) AGAINST (? $boolean) AS score
                     FROM $texttable
                     WHERE 1
                     AND MATCH (xar_text) AGAINST (? $boolean) > 0
                     AND xar_verse != 0
                     $sqllimits\n";
        $bindvars[] = $query;
        $bindvars[] = $query;

		if ($sort) {
	        switch($sort) {
	        case 'ref':
	            $sqlquery .= " ORDER BY xar_lid ASC\n";
	            break;
	        case 'score': default:
	            $sqlquery .= " ORDER BY score DESC\n";
	            break;
	        }

        // enable random ordering of results (for random block)
		} elseif ($rand) {
            $sqlquery .= "\nORDER BY RAND()";
		}

        $result = $textdbconn->SelectLimit($sqlquery, $numitems, $startnum-1, $bindvars); 

        if (!$result) return; 
        if ($result->EOF) return;

    } // END switch($searchtype)


    $lines = array();
    for (; !$result->EOF; $result->MoveNext()) {
        list($lid, $book, $chapter, $verse, $line, $tags, $score) = $result->fields;

        // merge tags back in with text
        $tags = @unserialize($tags);
        foreach ($tags as $offset => $content) {
            $line = substr_replace($line, $content, $offset, 0);
        }

        $lines[] = array('lid' => $lid,
                         'book' => $aliases[$book],
                         'chapter' => $chapter,
                         'verse' => $verse,
                         'text' => $line,
                         'score' => $score,
                         'ref' => "$aliases[$book] $chapter:$verse");
    }
    $result->Close(); 

    // finish assembly of result parameters
    $results['ref'] = $ref;
    $results['hitcount'] = $hitcount;
    $results['lines'] = $lines;
    $results['nextlimits'] = $nextlimits;

    return $results;

} 

?>
