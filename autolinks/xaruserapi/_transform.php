<?php

/**
 * $Id$
 * callback function to transform text dynamically (private)
 * @param $template_name name of template to execute
 * @param $matched_text the text that matched the autolink
 * @param $template_vars 
 * @returns string unparsed array of values to pass into the template
 * @return string of transformed matched text
 */
function autolinks_userapi__transform_preg($template_name, $matched_text, $template_vars)
{
    // This is the callback function for the dynamic template links.
    // This function is called for each dynamic link found, and the results
    // are passed to the relevant template. Errors are handled here.

    // Execute the template.
    $replace = xarTplModule(
        'Autolinks',
        xarModGetVar('autolinks', 'templatebase'),
        $template_name,
        $template_vars
     );

    // Catch any exceptions.
    if (xarExceptionValue()) {
        // The template errored.
        if (xarModGetVar('autolinks', 'showerrors') || xarVarIsCached('autolinks', 'showerrors')) {
            // Pass the error through the error template if required.
            // This mode of operation is used during setup.
            $replace = xarTplModule('Autolinks', 'error', 'match',
                array(
                    'match' => $matched_text,
                    'template_base' => xarModGetVar('autolinks', 'templatebase'),
                    'template_name' => $template_name,
                    'error_text' => xarVarPrepHTMLdisplay(xarExceptionRender('text'))
                )
            );
        } else {
            // Don't highlight the error - just return the matched text.
            // This is the normal mode of operation.
            $replace = $matched_text;
        }
 
        // Free up the error since we have handled it here.
        xarExceptionHandled();
    }

    // Put a placeholder in the spaces so we don't match it again.
    return preg_replace('/(\w)/', '$1ALSPACEHOLDER', $replace);
}

/**
 * $Id$
 * transform text (private)
 * @param $args['text'] or $args[0] string text
 * @returns string
 * @return string of transformed text
 */
function autolinks_userapi__transform($args)
{
    // Extra the arguments, allowing positional parameters.
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
        // Only get enabled and valid autolinks.
        // A valid autolink is one in which the replace template successfully parsed.
        if (!isset($lid)) {
            // Normal mode: go through all enabled autolinks.
            $tmpautolinks = xarModAPIFunc('autolinks', 'user', 'getall', array('enabled'=>true));
            $gotautolinks = 1;
        } else {
            // Test mode: just select one specific link.
            $tmpautolinks = array(xarModAPIFunc('autolinks', 'user', 'get', array('lid'=>$lid)));
            $alsearch = array();
            $alreplace = array();
        }

        // No Autolinks set up.
        if (empty($tmpautolinks)) {
            return $text;
        }

        // Create search/replace array from autolinks information
        foreach ($tmpautolinks as $tmpautolink) {
            // The replace text (whether a function or a straight string) is cached.
            $replace = $tmpautolink['cache_replace'];

            // Sanity check.
            if (empty($replace)) {
                continue;
            }

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
                . ')(?=[\s' . $punctuation . ']|$|ALPLACEHOLDER)' . '/is'
                . ($tmpautolink['dynamic_replace'] ? 'e' : '');

            // The replace will be either a straight string or a function to execute.
            if ($tmpautolink['dynamic_replace']) {
                // Dynamically execute the template when a match is found using a call-back.
                // TODO: check this: if the matched $1 contains single quotes, then according to the
                // PHP documentation, this should fail as '$1' matching "a'b" would evaluate as 'a'b'
                // which is invalid PHP. However, it seems to work. Does PHP escape out the single-
                // quotes for us?

                // If $replace cannot be evaluated as an expression, then we will get a pretty fatal error.
                // Test out the ability to evaluate the expression. This is just a final safety measure.
                if (!@eval('return ' . $replace . ';')) {
                    $replace = 'array()';
                }

                $alreplace[] = 'autolinks_userapi__transform_preg(\''
                    . $tmpautolink['template_name'] . '\', \'$1\', '
                    . $replace . ')';
            } else {
                // Replacement string.
                // Munge word boundaries of replace string to stop autolinks from linking to
                // themselves or other autolinks in step 2
                $alreplace[] = preg_replace('/(\w)/', '$1ALSPACEHOLDER', $replace);
            }
        }
    }

    // TODO: replace all this (steps 1 to 4) with the following:
    // 1. Split the text into two arrays: one with the tags and one with the content (preg_split).
    // 2. Join the content array into a string (implode).
    // 3. Do the link replacement on the string (preg_replace).
    // 4. Explode the content string back to an array (explode).
    // 5. Zip the two arrays back together into a string (array_join with a call-back to concatenate elements).
    // This has advantages in that each string at any point of the process is smaller than the current string;
    // we can exclude any arbitrary list of tags from having their content matched; we don't need to
    // loop for all tags, replacing them with placeholders, since they are simply moved aside into a separate
    // array. The technique works very well, but I need to do some performance tests first to ensure it
    // really is faster. Although 4 steps are replaced by 5, there are no loops whatsoever in the 5 steps.


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
