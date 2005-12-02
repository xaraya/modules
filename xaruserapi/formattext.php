<?php
/**
 * File: $Id:
 * 
 * Format text for displaying to screen
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
 * format text for displaying to screen
 * 
 * @author curtisdf 
 * @param  $args ['data'] results array
 * @param  $args ['strongs'] boolean, whether or not to show Strong's numbers
 * @param  $args ['sep'] whether or not we're doing a keyword search
 * @returns array
 * @return results array, or false on failure
 * @raise BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function bible_userapi_formattext($args)
{
    extract($args);

    // default is to format for Strong's
    if (!isset($strongs)) $strongs = true;

    if (!isset($data) || !is_array($data)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'data', 'user', 'formattext', 'Bible');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    // get list of source types this text should be parsed by
    $format = '';
    if (isset($data['text']['sourcetype'])) {
        if ($data['text']['sourcetype'] == 'OSIS') {
            $format = 'osis';
        } elseif ($data['text']['sourcetype'] == 'GBF') {
            $format = 'gbf';
        }
    }

    // format according to sourcetype
    if (!empty($format)) {
        for ($i = 0; $i < count($data['lines']); $i++) {
            $data['lines'][$i]['text'] = xarModAPIFunc('bible', 'user', "format_$format",
                                                       array('html' => $data['lines'][$i]['text'],
                                                             'strongs' => $strongs));
        }
    }

    // differential formatting based on search type
    if (!empty($lookup)) {
        $versesperpage = xarModGetVar('bible', 'user_lookupversesperpage');
        $data['versesperpage'] = $versesperpage;

        // do we have enough total verses for two columns?
        $data['halfway'] = '';
        if ($data['hitcount'] >= round($versesperpage/2)) {

            $data['twocols'] = true;

            // get page length
            $pagelength = 0;
            foreach ($data['lines'] as $index => $row) {
                $pagelength += strlen(strip_tags($row['text']));
            }
            $data['pagelength'] = $pagelength;

            // determine which verse the split occurs in
            $length = 0;
            foreach ($data['lines'] as $index => $row) {

                $linelength = strlen(strip_tags($row['text']));

                // if we pass the halfway point during this verse...
                if ($length + $linelength >= $pagelength/2) {
                    $data['halfway'] = $index;
                    break;
                }

                $length += $linelength;

            } // end "foreach verse in page"

        } else {
            $data['twocols'] = false;
        } // end "should we display 2 columns on this page?"


    } else {

        /* highlight any search words, leaving quoted words together */

        // generate array of words to highlight
        $wordstring = $data['query'];

        // translate any HTML entities back to their special characters
        $trans_table = array_flip(get_html_translation_table(HTML_ENTITIES));
        $wordstring = strtr($wordstring, $trans_table);

        // match quoted phrases in the query
        preg_match_all("/\"\w+([^\w ]\w+)?( \w+([^\w ]\w+)?)*\"/", $wordstring, $matches);
        $words = array();
        foreach ($matches[0] as $phrase) {
            $phrase = str_replace(' ', ' *(<[^>]+> *)*', $phrase); // account for tags in middle of words
            $phrase = str_replace('"', '', $phrase);
            $words[] = $phrase;
            $wordstring = preg_replace("/".addslashes($phrase)."/", '', $wordstring);
        }

        // now get everything else
        preg_match_all("/\w+([^\w ]\w+)?\*?/", $wordstring, $matches);
        foreach ($matches[0] as $index => $word) {
            $matches[0][$index] = str_replace('*', '\w*([^\w ]\w+)?', $matches[0][$index]);
        }
        $words = array_merge($matches[0], $words);
        $wordpattern = '('.join('|', $words).')';

        // add highlighting tags for each match
        foreach ($data['lines'] as $index => $row) {

            $line = $row['text'];

            preg_match_all("/\b$wordpattern\b/i", $line, $matches, PREG_OFFSET_CAPTURE);
            $matches[0] = array_reverse($matches[0]);
            foreach ($matches[0] as $match) {
                $match[0] = trim($match[0]);
/* we don't need this section !?!?!?!?
                if (preg_match("/ /", strip_tags($match[0]))) {

                    // grab all tags in middle of phrase and replace with </span>(tag)<span ...>
                    preg_match_all("/<[^>]+>/", $match[0], $tags, PREG_OFFSET_CAPTURE);
                    $tags[0] = array_reverse($tags[0]);
                    for ($i = 0; $i < count($tags[0]); $i++) {
                        $replace = '</span>'.$tags[0][$i][0].'<span class="bible-highlight">';
                        //$match[0] = substr_replace($match[0], $replace, $tags[0][$i][1], strlen($tags[0][$i][0]));
                    }
                }
*/
                $replace = '<span class="bible-highlight">'.$match[0].'</span>';
                $line = substr_replace($line, $replace, $match[1], strlen($match[0]));
            }
            $data['lines'][$index]['text'] = $line;

        }


    } // end "if it's a passage lookup or keyword search"

    return $data;

} 

?>
