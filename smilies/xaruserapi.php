<?php
/**
 * File: $Id: s.xaruserapi.php 1.7 03/02/17 16:20:32-05:00 John.Cox@mcnabb. $
 * 
 * Xaraya Smilies
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

/**
 * get all smilies
 * @returns array
 * @return array of links, or false on failure
 */
function smilies_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems)) {
        $numitems = -1;
    }

    $links = array();
    // Security Check
	if(!xarSecurityCheck('OverviewSmilies')) {
        return $links;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Get links
    $query = "SELECT xar_sid,
                     xar_code,
                     xar_icon,
                     xar_emotion
            FROM $smiliestable
            ORDER BY xar_emotion";
    $result =& $dbconn->SelectLimit($query, $numitems, $startnum-1);
    if (!$result) return;

    for (; !$result->EOF; $result->MoveNext()) {
        list($sid, $code, $icon, $emotion) = $result->fields;
        if (xarSecurityCheck('OverviewSmilies', 0)) {
            $links[] = array('sid'      => $sid,
                             'code'     => $code,
                             'icon'     => $icon,
                             'emotion'  => $emotion);
        }
    }

    $result->Close();

    return $links;
}

/**
 * get a specific smiley
 * @poaram $args['sid'] id of smiley to get
 * @returns array
 * @return link array, or false on failure
 */
function smilies_userapi_get($args)
{
    extract($args);

    if (!isset($sid)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'get', 'smilies');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    $smiliestable = $xartable['smilies'];

    // Get link
    $query = "SELECT xar_sid,
                   xar_code,
                   xar_icon,
                   xar_emotion
            FROM $smiliestable
            WHERE xar_sid = " . xarVarPrepForStore($sid);
    $result =& $dbconn->Execute($query);
    if (!$result) return;

    list($sid, $code, $icon, $emotion) = $result->fields;
    $result->Close();

    // Security Check
	if(!xarSecurityCheck('OverviewSmilies')) return;

    $link = array('sid'     => $sid,
                  'code'    => $code,
                  'icon'    => $icon,
                  'emotion' => $emotion);

    return $link;
}

/**
 * count the number of links in the database
 * @returns integer
 * @returns number of links in the database
 */
function smilies_userapi_countitems()
{
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();

    // Security Check
	if(!xarSecurityCheck('OverviewSmilies')) return;

    $smiliestable = $xartable['smilies'];

    $query = "SELECT COUNT(1)
            FROM $smiliestable";
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
function smilies_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'userapi', 'transform', 'smilies');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = smilies_userapitransform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = smilies_userapitransform($text);
        }
    } else {
        $transformed = smilies_userapitransform($extrainfo);
    }

    return $transformed;
}

function smilies_userapitransform($text)
{
    static $alsearch = array();
    static $alreplace = array();
    static $gotsmilies = 0;

    if (empty($gotsmilies)) {
        $gotsmilies = 1;
        $tmpsmilies = xarModAPIFunc('smilies', 'user', 'getall');

        // Create search/replace array from autolinks information
        foreach ($tmpsmilies as $tmpsmiley) {
            // Munge word boundaries to stop autolinks from linking to
            // themselves or other autolinks in step 2
            $tmpsmiley['icon'] = preg_replace('/(\b)/', '\\1ALSPACEHOLDER', $tmpsmiley['icon']);

            // Allow matches for smiles with < and > entities.
            $tmpsmiley['code'] = preg_quote($tmpsmiley['code'], '/');
            $tmpsmiley['code'] = str_replace(array('\>', '\<'), array('(?:&gt;|>)', '(?:&lt;|<)'), $tmpsmiley['code']);

            // Note use of assertions here to only match specific words,
            // for instance ones that are not part of a hyphenated phrase
            // or (most) bits of an email address
            $alsearch[] = '/(?<![\w@\.:-])(' . $tmpsmiley['code'] . ')(?![\w@:-])(?!\.\w)/i';
            $alreplace[] = '<img src="' . htmlspecialchars($tmpsmiley['icon']) .
                           '" alt="' . htmlspecialchars(xarML($tmpsmiley['emotion'])) .
                           '" title="' . htmlspecialchars(xarML($tmpsmiley['emotion'])) .
                           '" />';
        }
    }

    // Step 1 - move all tags out of the text and replace them with placeholders
    preg_match_all('/(<\w[^>]+>)/i', $text, $matches);
    $matchnum = count($matches[1]);
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace('/' . preg_quote($matches[1][$i], '/') . '/', "ALPLACEHOLDER{$i}PH", $text, 1);
    }

    // Step 2 - put the smilies in.
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