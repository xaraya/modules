<?php

/**
 * Various text transforms, in one convenient package.
 *
 * Transforms supported (and these can be chained):
 * - Strip out HTML, leaving newlines (text='text')
 * - Strip newlines (nonl=true)
 * - Convert to HTML (force to HTML) (html='text')
 * - Limit text to given number of words (maxwords=N)
 *
 * By default, all line endings will be converted to newlines.
 *
 * @param text string The input text to be transformed
 * @param maxwords int The maximum number of words to display (will also strip HTML)
 * @param maxwords_message string An alternative message to display when the text is truncated.
 * @param text string Characters (possibly HTML) that will be transformed into text
 * @param format boolean If set to 'html' will force 'text' to be transformed to HTML
 * @param html strng Characters (possibly text) that will be transformed into HTML
 * @return string The transformed text
 *
 * @todo Get these into the base module.
 */

function ievents_userapi_transform($args)
{
    extract($args);

    // Input can be passed in, in one of two separate parameters, to help
    // keeps code size down in templates.
    if (isset($text) && is_string($text)) {
        $text_in = $text;
        if (!isset($format) && $format != 'html') $format = 'text';
    }
    if (isset($html) && is_string($html)) {
        $text_in = $html;
        $format = 'html';
    }

    // Convert newline/carriage return pairs to newlines
    $text_in = str_replace(array("\n\r", "\r\n"), "\n", $text_in);

    // Check if we need to limit the work count.
    if (!empty($maxwords) && is_numeric($maxwords) && $maxwords > 0 && str_word_count($text_in) > $maxwords) $reduce_words = true;

    if ((!empty($format) && $format == 'text') || !empty($reduce_words)) {
        // Transform to text - strip out HTML
        $text_in = strip_tags($text_in);
        // TODO: get the system charset, and ensure html_entity_decode is aware of it.
        // (although UTF-8 is only supported from PHP5 anyway)
        $text_in = html_entity_decode($text_in, ENT_QUOTES);
    }

    if (!empty($nonl)) {
        // Strip out newlines - make into one single line.
        // Suck surrounding spaces into the match.
        $text_in = preg_replace('/[ ]*[\n\r]+[ ]*/', ' ', $text_in);
    }

    if (!empty($reduce_words)) {
        // Limit to a maximum number of words.
        // It is only sensible to apply this to text, since html
        // cannot be easily truncated without tag mismatching problems.

        $word_array = str_word_count($text_in, 2);
        $total_words = count($word_array);

        // Find the word we need.
        $keys = array_keys($word_array);
        $pos = $keys[$maxwords];

        if ($pos > 1) {
            $text_in = substr($text_in, 0, $pos-1);
            // #(1) - total words
            // #(2) - words not shown
            // #(3) - (maximum) words display

            // Message parameter can be passed in.
            if (!isset($maxwords_message)) $maxwords_message = '... (#(1) words total)';
            $text_in .= xarML($maxwords_message, $total_words, $total_words - $maxwords, $maxwords);
        }
    }

    if (!empty($format) && $format == 'html') {
        // Transform to html
        $text_in = ievents_userapi_transform_smart_html($text_in);
    }

    return $text_in;
}

/*
 * Do smart conversion to HTML.
 * This should be a core function. It is copied from the
 * html module, but since that transform is controlled by global html
 * settings, it is duplicated here.
 * If the text is already HTML, this function will not actually be performing
 * any transform.
 */

function ievents_userapi_transform_smart_html($text)
{
    if (strlen(trim($text)) == 0) return '';

    $dobreak = 1;

    // Just to make things a little easier, pad the end
    $text = $text . "\n";

    // Create a few entities where required
    // TODO: transform < and > where they do not form part of a tag
    $text = preg_replace('/&(?!#{0,1}[a-z0-9]+;)/i', "&amp;", $text);

    $text = preg_replace('|<br />\s*<br />|', "\n\n", $text);

    $text = preg_replace('!(<(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)!', "\n$1", $text);

    $text = preg_replace('!(</(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])>)!', "$1\n\n", $text);

    // Cross-platform newlines
    $text = str_replace(array("\r\n", "\r"), "\n", $text);

    // Take care of duplicaten newlines - turns runs of two or more into just two (treated as paragraphs)
    $text = preg_replace("/\n\n+/", "\n\n", $text);

    // Make paragraphs, including one at the end
    $text = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $text);

    // Under certain strange conditions it could create a P of entirely whitespace
    $text = preg_replace('|<p>\s*?</p>|', '', $text);

    $text = preg_replace('!<p>\s*(</?(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|hr|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)\s*</p>!', "$1", $text);

    // Problem with nested lists
    $text = preg_replace("|<p>(<li.+?)</p>|", "$1", $text);

    $text = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $text);

    $text = str_replace('</blockquote></p>', '</p></blockquote>', $text);

    $text = preg_replace('!<p>\s*(</?(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|hr|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)!', "$1", $text);

    $text = preg_replace('!(</?(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)\s*</p>!', "$1", $text);

    // Optionally line breaks
    // TODO: this implies that the 'do simple linebreaks' option is both a transform in its own
    // right *and* a modifier to the smart transform. Or perhaps it is the other way around?
    // Can be make the options a little more clear.
    //if ($dobreak == 1) $text = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $text);

    // Remove all <br>s after a block tag
    $text = preg_replace('!(</?(?:table|thead|tfoot|caption|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)\s*<br />!', "$1", $text);

    // A <br/> for a single newline, on its own, with no tags immediately surrounding it.
    // This allows breaks within a paragraph (where double-newlines define the paragraphs)
    // Preserve any additional white space
    $text =  preg_replace('/([^>]\s*)[\n](\s*[^<])/', '$1<br/>'."\n".'$2', $text);

    // Remove a <br> before a block tag
    // TODO: this does not include all block tags, h1-6, tables etc?
    $text = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)!', '$1', $text);

    // Remove paragraphs and breaks from within any <pre> tags.
    $text = preg_replace('!(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(ievents_userapi_transform_clean_pre('$2'))  . '</pre>' ", $text);

    // Since this is HTML now, it can be safely trimmed.
    $text = trim($text);

    return $text;
}

// Remove paragraphs and breaks from within any <pre> tags.
function ievents_userapi_transform_clean_pre($text) {
	$text = str_replace('<br />', '', $text);
	$text = str_replace('<p>', "\n", $text);
	$text = str_replace('</p>', '', $text);
	return $text;
}

?>