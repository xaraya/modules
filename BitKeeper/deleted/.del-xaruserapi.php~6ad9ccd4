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
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = censor_userapitransform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = censor_userapitransform($text);
        }
    } else {
        $transformed = censor_userapitransform($extrainfo);
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