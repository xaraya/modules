<?php 
/**
 * File: $Id$
 * 
 * Xaraya Censor
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Censor Module
 * @author John Cox
*/

/**
 * get all links
 * @returns array
 * @return array of links, or false on failure
 */
function censor_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $censors = array();
    if (!xarSecAuthAction(0, 'censor::', '::', ACCESS_READ)) {
        return $censors;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $censortable = $xartable['censor'];

    // Get links
    $query = "SELECT xar_cid,
                   xar_keyword
            FROM $censortable
            ORDER BY xar_keyword";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($cid, $keyword) = $result->fields;
        if (xarSecAuthAction(0, 'censor::', "$keyword::$cid", ACCESS_READ)) {
            $censors[] = array('cid' => $cid,
                               'keyword' => $keyword);
        }
    }

    $result->Close();

    return $censors;
}

/**
 * get a specific link
 * @poaram $args['cid'] id of link to get
 * @returns array
 * @return link array, or false on failure
 */
function censor_userapi_get($args)
{
    extract($args);

    if (!isset($cid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $censortable = $xartable['censor'];

    // Get link
    $query = "SELECT xar_cid,
                   xar_keyword
            FROM $censortable
            WHERE xar_cid = " . xarVarPrepForStore($cid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($cid, $keyword) = $result->fields;
    $result->Close();

    if (!xarSecAuthAction(0, 'censor::', "$keyword::$cid", ACCESS_READ)) {
        return false;
    }
    $censor = array('cid' => $cid,
                  'keyword' => $keyword);

    return $censor;
}

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function censor_userapi_countitems()
{
    list($dbconn) = xarDBGetConn();
    $xartable = xarDBGetTables();

    $censortable = $xartable['censor'];

    $query = "SELECT COUNT(1)
            FROM $censortable";
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($numitems) = $result->fields;

    $result->Close();

    return $numitems;
}

/**
 * transform text
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function censor_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'transform', 'censor');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = censor_userapitransform($text);
        }
    } else {
        $transformed = censor_userapitransform($text);
    }

    return $transformed;
}

function censor_userapitransform($text)
{
    static $alsearch = array();
    static $alreplace = array();
    static $gotcensor = 0;

    if (empty($gotcensor)) {
        $gotcensor = 1;
        xarModAPILoad('censor', 'user');
        $tmpcensors = xarModAPIFunc('censor', 'user', 'getall');

        // Create search/replace array from censor information
        foreach ($tmpcensors as $tmpcensor) {
            // Note use of assertions here to only match specific words,
            // for instance ones that are not part of a hyphenated phrase
            // or (most) bits of an email address
            $alsearch[] = '/(?<![\w@\.:-])(' . preg_quote($tmpcensor['keyword'], '/'). ')(?![\w@:-])(?!\.\w)/i';
            $alreplace[] = xarModGetVar('censor', 'replace');
        }
    }

    // Step 1 - move all tags out of the text and replace them with placeholders
    preg_match_all('/(<a\s+.*?\/a>|<[^>]+>)/i', $text, $matches);
    $matchnum = count($matches[1]);
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace('/' . preg_quote($matches[1][$i], '/') . '/', "ALPLACEHOLDER{$i}PH", $text, 1);
    }

    $text = preg_replace($alsearch, $alreplace, $text);

    // Step 3 - replace the spaces we munged in step 2
    $text = preg_replace('/ALSPACEHOLDER/', '', $text);

    // Step 4 - replace the HTML tags that we removed in step 1
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace("/ALPLACEHOLDER{$i}PH/", $matches[1][$i], $text, 1);
    }


    return $text;
}

?>