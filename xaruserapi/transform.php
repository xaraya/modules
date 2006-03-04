<?php
/*
 * Censor Module
 *
 * @package modules
 * @copyright (C) 2003 by the Xaraya Development Team
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.com
 * @subpackage  Censor Module
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
        $msg = xarML('Invalid Parameter Count');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = transform($text);
        }
    } else {
        $transformed = transform($extrainfo);
    }

    return $transformed;
}

function transform($text)
{
    static $alsearch = array();
    static $alreplace = array();
    static $gotcensor = 0;

    $local = xarSessionGetVar('navigationLocale');

    if (empty($gotcensor)) {
        $gotcensor = 1;
        $tmpcensors = xarModAPIFunc('censor',
                                    'user',
                                    'getall1',
                                    array('local' => $local));

        // Create search/replace array from censor information
        foreach ($tmpcensors as $tmpcensor) {

            // Note use of assertions here to only match specific words,
            // for instance ones that are not part of a hyphenated phrase
            // or (most) bits of an email address
           //var_dump($tmpcensor);
            if ($tmpcensor['case_sensitive'] == 0) {
                      if ($tmpcensor['match_case'] == 0) {
                          $alsearch[] = '/(?<![\w@\.:-])(' . preg_quote($tmpcensor['keyword'], '/'). ')(?![\w@:-])(?!\.\w)/i';
                      } elseif ($tmpcensor['match_case'] == 1) {
                            $alsearch[] = '/(?<![\w@\.:-])(' . preg_quote($tmpcensor['keyword'], '/'). ')/i';
                      } elseif ($tmpcensor['match_case'] == 2) {
                            $alsearch[] = '/(' . preg_quote($tmpcensor['keyword'], '/'). ')(?![\w@:-])(?!\.\w)/i';
                      } elseif ($tmpcensor['match_case'] == 3) {
                            $alsearch[] = '/' . preg_quote($tmpcensor['keyword'], '/'). '/i';
                          }
              } else {
              if ($tmpcensor['match_case'] == 0) {
                          $alsearch[] = '/(?<![\w@\.:-])(' . preg_quote($tmpcensor['keyword'], '/'). ')(?![\w@:-])(?!\.\w)/';
                      } elseif ($tmpcensor['match_case'] == 1) {
                            $alsearch[] = '/(?<![\w@\.:-])(' . preg_quote($tmpcensor['keyword'], '/'). ')/';
                      } elseif ($tmpcensor['match_case'] == 2) {
                            $alsearch[] = '/(' . preg_quote($tmpcensor['keyword'], '/'). ')(?![\w@:-])(?!\.\w)/';
                      } elseif ($tmpcensor['match_case'] == 3) {
                            $alsearch[] = '/' . preg_quote($tmpcensor['keyword'], '/'). '/';
            }

            }
            $alreplace[] = xarModGetVar('censor', 'replace');
        }
    }


    preg_match_all('/(<a\s+.*?\/a>|<("[^"]*"|\'[^\']*\'|[^\'">])*>)/i', $text, $matches);

    $matchnum = count($matches[1]);
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace('/' . preg_quote($matches[1][$i], '/') . '/', "&nbsp;ALPLACEHOLDER{$i}PH&nbsp;", $text, 1);
    }

    $text = preg_replace($alsearch, $alreplace, $text);

    // Step 3 - replace the spaces we munged in step 2
    $text = preg_replace('/ALSPACEHOLDER/', '', $text);

    // Step 4 - replace the HTML tags that we removed in step 1
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace("/&nbsp;ALPLACEHOLDER{$i}PH&nbsp;/", $matches[1][$i], $text, 1);

 }

    return $text;
}

?>
