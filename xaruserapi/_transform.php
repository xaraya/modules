<?php

// Divert PHP errors to the normal error stack
function autolinks_userapi__transform_errhandler($errorType, $errorString, $errorFile, $errorLine)
{
    //if (!error_reporting()) {return;}
    if (!error_reporting() || !($errorType & (E_ALL | E_NOTICE | E_WARNING))) return;
    $msg = "File: " . $errorFile. "; Line: " . $errorLine . "; ". $errorString;
    xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
    return;
}

/**
 * $Id$
 * callback function to transform text dynamically (private)
 * @param $template_name name of template to execute
 * @param $matched_text the text that matched the autolink
 * @param $template_vars 
 * @returns string unparsed array of values to pass into the template
 * @return string of transformed matched text
 */
function autolinks_userapi__transform_preg($template_name, $matched_text, $template_vars, $munger)
{
    // This is the callback function for the dynamic template links.
    // This function is called for each dynamic link found, and the results
    // are passed to the relevant template. Errors are handled here.

    // Execute the template.
    set_error_handler('autolinks_userapi__transform_errhandler');

    $replace = xarTplModule(
        'autolinks',
        xarModGetVar('autolinks', 'templatebase'),
        $template_name,
        $template_vars
    );
    restore_error_handler();

    // Catch any exceptions.
    if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
        // The template errored.

        // 'text' rendering returns the exception as an array.
        $errorstack = xarErrorGet();
        $errorstack = array_shift($errorstack);
        $error_text = $errorstack['long'];

        // Clear the errors since we are handling it locally.
        xarErrorHandled();

        if (xarModGetVar('autolinks', 'showerrors') || xarVarIsCached('autolinks', 'showerrors')) {
            // Pass the error through the error template if required.
            // This mode of operation is used during setup.
            $replace = xarTplModule('autolinks', 'error', 'match',
                array(
                    'match' => $matched_text,
                    'template_base' => xarModGetVar('autolinks', 'templatebase'),
                    'template_name' => $template_name,
                    'error_text' => $error_text
                )
            );
            if (xarCurrentErrorType() <> XAR_NO_EXCEPTION) {
                // The error template errored - just return the matched text.
                xarErrorHandled();
                $replace = $matched_text;
            }
        } else {
            // Don't highlight the error - just return the matched text.
            // This is the normal mode of operation.
            $replace = $matched_text;
        }
 
        // Handle the error since we have rendered it here.
        xarErrorHandled();
    } else {
        // Trim whitespace from template output by default, until we have a choice
        // on how to handle whitespace from within the template.
        $replace = trim($replace);
    }

    // Put a placeholder in the word boundaries so we don't match it again.
    return preg_replace('/\b/', "$munger", $replace);
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
    static $alsearch;
    static $alreplace;
    static $gotautolinks = 0;
    static $tag_preg = '';
    static $joiner = '';
    static $munger = '';

    // Extra the arguments, allowing positional parameters.
    extract($args, EXTR_PREFIX_INVALID, 'p');

    if (!isset($text)) {
        $text = (isset($p_0) ? $p_0 : '');
    }

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

    // Get two tokens that do not appear in the original text.
    // Try a few tokens so we can be sure it will work without inadvertantly
    // matching valid content. Hopefully the first token will work (' #-+-# ')
    // Use the saved version unless they happen to appear in this block of text,
    // for which the complete replace string array needs to be rebuilt.
    if ($joiner == '' || strpos($text, $joiner) || strpos($text, $munger)) {
        $joiner = '';
        $munger = '';
        $gotautolinks = 0;
        for ($i=0; $i<=11; $i+=2)
        {
            $try = '#' . str_pad('-', $i, '+', STR_PAD_BOTH) . '#';
            if (strpos($text, $try) === false)
            {
                if (empty($joiner)) {
                    $joiner = ' ' . $try . ' ';
                } else {
                    $munger = $try;
                break;
                }
            }
        }
    }

    if (empty($joiner) || empty($munger)) {
        // We can't do a transform due to matches in the content.
        return $text;
    }

    if ($gotautolinks == 0) {
        $alsearch = array();
        $alreplace = array();
    }

    if (empty($gotautolinks)) {
        if (!isset($lid)) {
            // Normal mode: go through all enabled autolinks.
            // Order by name so the admin has some control over the order in which the
            // links are applied to content (some links may need to stack on top of
            // each other).
            // Only get enabled autolinks.
            $tmpautolinks = xarModAPIFunc(
                'autolinks', 'user', 'getall',
                array('enabled'=>true, 'order'=>'name')
            );
            // Make sure we don't visit this section again.
            $gotautolinks = 1;
        } else {
            // Test/sample mode: just select one specific link.
            $tmpautolinks = array(xarModAPIFunc('autolinks', 'user', 'get', array('lid'=>$lid)));
            // Clear the previous test link.
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
                // All the special characters can be retained except for the preg
                // enclosing character ('/') so escape just that character.
                $keywordre = preg_replace(
                    '#/#', '\/',
                    $tmpautolink['keyword']
                );
            } else {
                // The keyword has not been entered as an RE, so treat it as a pure
                // string by quoting and escaping special preg characters.
                $keywordre = preg_quote($tmpautolink['keyword'], '/');

                // Allow whitespace matching to be a bit looser.
                $keywordre = preg_replace('/\s+/', $wspreg . '+', $keywordre);
            }

            // Note use of assertions here to only match specific words.
            $alsearch[] = '/' . '(?<=[\s' . $punctuation . ']|^)('
                . $keywordre
                . ')(?=[\s' . $punctuation . ']|$)' . '/is'
                . ($tmpautolink['dynamic_replace'] ? 'e' : '');

            // The replace will be either a straight string or a function to execute.
            if ($tmpautolink['dynamic_replace']) {
                // Dynamically execute the template when a match is found using a call-back.
                // TODO: check this: if the matched $1 contains single quotes, then according to the
                // PHP documentation, this should fail as '$1' matching "a'b" would evaluate as 'a'b'
                // which is invalid PHP. However, it seems to work. Does PHP escape out the single-
                // quotes for us?

                // If $replace cannot be evaluated as an expression, then we will get a system error later.
                // Test out the ability to evaluate the expression. This is just a final safety measure.
                if (!@eval('return ' . $replace . ';')) {
                    // The string did not evaluate - set it to something safe.
                    $replace = 'array()';
                }

                $alreplace[] = 'autolinks_userapi__transform_preg(\''
                    . $tmpautolink['template_name'] . '\', \'$1\', '
                    . $replace . ', \'' . $munger . '\')';
            } else {
                // Replacement string.
                // Munge the word boundaries to prevent a recursive match.
                $alreplace[] = preg_replace('/([^a-z])([a-z])/i', '$1'.$munger.'$2', $replace);
            }
        }
    }

    if ($tag_preg == '')
    {
        // List of elements we do not want to match inside.
        // The user can enter a list in any format they wish. It can also
        // include custom tags if the user wants to prevent Autolink matching
        // in any enclosed section of a document.

        $exclude_elements = xarModGetVar('autolinks', 'excludeelements');
        if (empty($exclude_elements)) {$exclude_elements = 'a';}

        // The tag_preg will contain the preg matches to identify spans of
        // tags and elements that should not be matched by this module. Open with
        // a HTML comment. The comments should also protect inline Javascript.
        $tag_preg = '/(?:' . "<!--.*?-->$wspreg*";

        // Build REs for list of elements we won't be matching in.
        // These are non-empty elements, e.g. <a [anything]>[anything]</a>
        if (!empty($exclude_elements)) {
            foreach (explode(' ', $exclude_elements) as $exclude_element)
            {
                // Use back-assertion "(?<!\/)>" (closing brace '>' not preceded by '/')
                // to ensure the first tag is not closed.
                $tag_preg .= '|' . "<" . $exclude_element . "[^>]*(?<!\/)>"
                   . ".*?" . "<\/" . $exclude_element . ">$wspreg*";
            }
        }

        // Expression for matching any remaining tags.
        // These are <tag ...>, </tag> and <tag .../>
        $tag_preg .= "|<\/?\w+[^>]*>$wspreg*";

        // Close the tag preg.
        $tag_preg .= ')+/is';

        // Now we have a static string containing a preg pattern for splitting
        // up HTML or XHTML content into areas that can be Autolinked and areas
        // that should not, i.e. into tags and disallowed-tag contents, and into
        // content between those tags.
    }

    // This is what happens in summary:
    // 1. Split the text into two arrays: one with the tags and one with the content (preg_split).
    // 2. Join the content array into a string (implode).
    // 3. Do the link replacement on the string (preg_replace).
    // 4. Explode the content string back to an array (explode).
    // 5. Zip the two arrays back together into a string (array_join with a call-back to concatenate elements).
    
    // Get array of content.
    $content_array = preg_split($tag_preg, $text);

    // Get array of tags. The array we need will be put into
    // $tag_array[0]. There will be no higher-level elements
    // to the array as there are no sub-patterns in tag_preg.
    preg_match_all($tag_preg, $text, $tag_array);

    $limit = xarModGetVar('autolinks', 'maxlinkcount');
    if (empty($limit))
    {
        $limit = -1;
    }

    // Do the content replacement (create the links).
    // The array is flattened to a string, the replace is done, then exploded
    // back into an array. It seems to be faster than doing the substitution
    // directly on the content array (probably cartesian product), and also
    // allows a limit to the number of matches to be set.
    $content_array = explode(
        $joiner,
        preg_replace($alsearch, $alreplace, implode($joiner, $content_array), $limit)
    );

    // Zip the two arrays back together.
    // Looping for each element and building a string is slow; array_map seems to be fast enough.
    $func_join_strings = create_function('$m,$n', 'return (!empty($m)?$m:"") . (!empty($n)?$n:"");');
    $text = implode('', array_map($func_join_strings, $content_array, $tag_array[0]));

    // Strip out munger characters.
    return str_replace($munger, '', $text);
}

?>
