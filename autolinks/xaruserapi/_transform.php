<?php

/**
 * $Id$
 * transform text (private)
 * @param $args['text'] or $args[0] string text
 * @returns string
 * @return string of transformed text
 */

function autolinks_userapi__transform($args)
{
    extract($args, EXTR_PREFIX_INVALID, 'p');

    if (!isset($text)) {
        $text = (isset($p_0) ? $p_0 : '');
    }

    static $alsearch = array();
    static $alreplace = array();
    static $gotautolinks = 0;

    $nbsp_as_whitespace = xarModGetVar('autolinks', 'nbspiswhite');

    // General-purpose single-whitespace character match.
    // This optionally includes non-breaking space entities.
    if ($nbsp_as_whitespace == 1)
    {
        $wspreg = '(?:&nbsp;|[\s])';
    } else {
        $wspreg = '(?:[\s])';
    }

    // These are the punctuation characters that are allowed to sit
    // adjacent to matched words. Include a hyphon for words in hyphonated-
    // pairs to be matched. Include a full-stop (period) for words at the
    // end of sentances to be matched.
    $punctuation = xarModGetVar('autolinks', 'punctuation');

    // Escape characters that are special in a character class definition.
    // These are: ] ^ -
    $punctuation = preg_replace('/([-^\]])/', '\\\$1', $punctuation);

    if (empty($gotautolinks)) {
        $gotautolinks = 1;

        // Only get enabled and valid autolinks.
        // A valid autolink is one in which the replace template successfully parsed.
        $tmpautolinks = xarModAPIFunc('autolinks', 'user', 'getall', array('enabled'=>true, 'valid'=>true));

        // No Autolinks set up.
        if (empty($tmpautolinks)) {
            return $text;
        }

        // Create search/replace array from autolinks information
        foreach ($tmpautolinks as $tmpautolink) {
            // TODO: this will be cached in the autolinks table.
            $replace = xarModAPIfunc('autolinks', 'user', 'getreplace', array('link'=> &$tmpautolink));

            if ($replace['status']) {
                if ($tmpautolink['match_re'])
                {
                    // The keyword has been entered as an RE, so use it as it comes.
                    // Strip off /slashes/ and options/ims that may have been entered by the user.
                    $keywordre = preg_replace(array('/^\//', '/\/[\w]*$/'), '', $tmpautolink['keyword']);
                } else {
                    // The keyword has not been entered as an RE, so make it into one.
                    // Quote special characters.
                    $keywordre = preg_quote($tmpautolink['keyword'], '/');

                    // Allow whitespace matching to be a bit looser.
                    $keywordre = preg_replace('/\s+/', $wspreg . '+', $keywordre);
                }

                // Note use of assertions here to only match specific words.
                $alsearch[] = '/' . '(?<=[\s' . $punctuation . ']|^|ALPLACEHOLDER\dPH)('
                    . $keywordre
                    . ')(?=[\s' . $punctuation . ']|$|ALPLACEHOLDER)' . '/is';

                // Munge word boundaries of replace string to stop autolinks from linking to
                // themselves or other autolinks in step 2
                $alreplace[] = preg_replace('/(\w)/', '$1ALSPACEHOLDER', $replace['replace']);
            }
        }
    }

    // Step 1 - move all tags out of the text and replace them with placeholders
    preg_match_all('/(<a\s+.*?\/a>|<[^>]+>)/i', $text, $matches);
    $matchnum = count($matches[1]);
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace('/' . preg_quote($matches[1][$i], '/') . '/', "ALPLACEHOLDER{$i}PH", $text, 1);
    }

    // Step 2 - s/r of the remaining text
    $maxlinkcount = xarModGetVar('autolinks', 'maxlinkcount');
    $text = preg_replace($alsearch, $alreplace, $text, ($maxlinkcount >= 1 ? $maxlinkcount : (-1)));

    // Step 3 - replace the spaces we munged in step 2
    $text = preg_replace('/ALSPACEHOLDER/', '', $text);

    // Step 4 - replace the HTML tags that we removed in step 1
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace("/ALPLACEHOLDER{$i}PH/", $matches[1][$i], $text, 1);
    }

    return $text;
}

?>
