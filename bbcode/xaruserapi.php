<?php
/**
 * File: $Id$
 * 
 * Xaraya BBCode
 * Based on pnBBCode Hook from larseneo
 * Converted to Xaraya by John Cox
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage BBCode Module
 * @author larseneo
*/

// the hook function
//
function bbcode_userapi_transform($args) 
{
    extract($args);

    // Argument check
    if ((!isset($objectid)) ||
        (!isset($extrainfo))) {
        $msg = xarML('Invalid Parameter Count',
                    join(', ',$invalid), 'admin', 'create', 'bbcode');
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
                       new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = bbcode_transform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        foreach ($extrainfo as $text) {
            $result[] = bbcode_transform($text);
        }
    } else {
        $result = bbcode_transform($text);
    }

    return $result;
}

// the wrapper for a string var (simple up to now)
//
function bbcode_transform($text) 
{
    $message = bbcode_encode($text, $is_html_disabled=false);
    return $message;
}


/**
 * bbdecode/bbencode functions:
 * Rewritten - Nathan Codding - Aug 24, 2000
 * quote, code, and list rewritten again in Jan. 2001.
 * All BBCode tags now implemented. Nesting and multiple occurances should be
 * handled fine for all of them. Using str_replace() instead of regexps often
 * for efficiency. quote, list, and code are not regular, so they are 
 * implemented as PDAs - probably not all that efficient, but that's the way it is. 
 *
 * Note: all BBCode tags are case-insensitive.
 *
 * some changes for PostNuke: larsneo - Jan, 12, 2003
 * different [img] tag conversion against XSS
 */

function bbcode_encode($message, $is_html_disabled) 
{

    // pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
    // This is important; bbencode_quote(), bbencode_list(), and bbencode_code() all depend on it.
    $message = " " . $message;

    // [CODE] and [/CODE] for posting code (HTML, PHP, C etc etc) in your posts.
    $message = bbcode_encode_code($message, $is_html_disabled);

    // change newlines to <br />'s
    $dotransform = xarModGetVar('bbcode', 'dotransform');
    if ($dotransform == 1){
        $transformtype = xarModGetVar('bbcode', 'transformtype');
        if ($transformtype == 1){
            $message = preg_replace("/\n/si","<br />",$message);
        } elseif ($transformtype == 2){
            $message = preg_replace("/\n/si","</p><p>",$message);
        }
        $message = str_replace ("<p></p>", "", $message);
    }

    
    // First: If there isn't a "[" and a "]" in the message, don't bother.
    if (! (strpos($message, "[") && strpos($message, "]")) )
    {
        // Remove padding, return.
        $message = substr($message, 1);
        return $message;    
    }

    // [QUOTE] and [/QUOTE] for posting replies with quote, or just for quoting stuff.    
    $message = bbcode_encode_quote($message);

    // [list] and [list=x] for (un)ordered lists.
    $message = bbcode_encode_list($message);

    // [u] and [/u] for underline text.
    $message = preg_replace("/\[u\](.*?)\[\/u\]/si", "<u>\\1</u>", $message);

    // [b] and [/b] for bolding text.
    $message = preg_replace("/\[b\](.*?)\[\/b\]/si", "<b>\\1</b>", $message);

    // [i] and [/i] for italicizing text.
    $message = preg_replace("/\[i\](.*?)\[\/i\]/si", "<i>\\1</i>", $message);

    // [p] and [/p] for paragraphs
    $message = preg_replace("/\[p\](.*?)\[\/p\]/si", "<p>\\1</p>", $message);

    // [sub] and [/sub] for subscripts
    $message = preg_replace("/\[sub\](.*?)\[\/sub\]/si", "<sub>\\1</sub>", $message);

    // [sup] and [/sup] for superscripts
    $message = preg_replace("/\[sup\](.*?)\[\/sup\]/si", "<sup>\\1</sup>", $message);

    // [color=xxx] [/color] for text color
    $message = preg_replace("/\[color\=([a-zA-Z0-9_-]+)\](.*?)\[\/color\]/si", "<span style='color: \\1;'>\\2</span>", $message);

    // [size=xxx] [/size] for text size
    $message = preg_replace("/\[size\=([a-zA-Z0-9.-]+)\](.*?)\[\/size\]/si", "<span style='font-size: \\1;'>\\2</span>", $message);

    // [img]image_url_here[/img] code..
    $message = preg_replace("#\[img\](http://)?(.*?)\[/img\]#si", "<img src=\"http://\\2\" />", $message);
    //$message = preg_replace("/\[img\](.*?)\[\/img\]/si", "<img src=\"\\1\" border=\"0\" />", $message);

    // Patterns and replacements for URL and email tags..
    $patterns = array();
    $replacements = array();

    // [url]xxxx://www.phpbb.com[/url] code..
    $patterns[0] = "#\[url\]([a-z]+?://){1}(.*?)\[/url\]#si";
    $replacements[0] = '<a href="\1\2">\1\2</a>';

    // [url]www.phpbb.com[/url] code.. (no xxxx:// prefix).
    $patterns[1] = "#\[url\](.*?)\[/url\]#si";
    $replacements[1] = '<a href="http://\1">\1</a>';

    // [url=xxxx://www.phpbb.com]phpBB[/url] code..
    $patterns[2] = "#\[url=([a-z]+?://){1}(.*?)\](.*?)\[/url\]#si";
    $replacements[2] = '<a href="\1\2">\3</a>';

    // [url=www.phpbb.com]phpBB[/url] code.. (no xxxx:// prefix).
    $patterns[3] = "#\[url=(.*?)\](.*?)\[/url\]#si";
    $replacements[3] = '<a href="http://\1">\2</a>';

    // [email]user@domain.tld[/email] code..
    $patterns[4] = "#\[email\](.*?)\[/email\]#si";
    $replacements[4] = '<a href="mailto:\1">\1</a>';

    $message = preg_replace($patterns, $replacements, $message);

    // Remove our padding from the string..
    $message = substr($message, 1);
    return $message;

} // bbcode_encode()



/**
 * Nathan Codding - Jan. 12, 2001.
 * Performs [quote][/quote] bbencoding on the given string, and returns the results.
 * Any unmatched "[quote]" or "[/quote]" token will just be left alone. 
 * This works fine with both having more than one quote in a message, and with nested quotes.
 * Since that is not a regular language, this is actually a PDA and uses a stack. Great fun.
 *
 * Note: This function assumes the first character of $message is a space, which is added by 
 * bbencode().
 */
function bbcode_encode_quote($message)
{
    // First things first: If there aren't any "[quote]" strings in the message, we don't
    // need to process it at all.
    
    if (!strpos(strtolower($message), "[quote]"))
    {
        return $message;    
    }
    
    $stack = Array();
    $curr_pos = 1;
    while ($curr_pos && ($curr_pos < strlen($message)))
    {    
        $curr_pos = strpos($message, "[", $curr_pos);
    
        // If not found, $curr_pos will be 0, and the loop will end.
        if ($curr_pos)
        {
            // We found a [. It starts at $curr_pos.
            // check if it's a starting or ending quote tag.
            $possible_start = substr($message, $curr_pos, 7);
            $possible_end = substr($message, $curr_pos, 8);
            if (strcasecmp("[quote]", $possible_start) == 0)
            {
                // We have a starting quote tag.
                // Push its position on to the stack, and then keep going to the right.
                array_push($stack, $curr_pos);
                ++$curr_pos;
            }
            else if (strcasecmp("[/quote]", $possible_end) == 0)
            {
                // We have an ending quote tag.
                // Check if we've already found a matching starting tag.
                if (sizeof($stack) > 0)
                {
                    // There exists a starting tag. 
                    // We need to do 2 replacements now.
                    $start_index = array_pop($stack);

                    // everything before the [quote] tag.
                    $before_start_tag = substr($message, 0, $start_index);

                    // everything after the [quote] tag, but before the [/quote] tag.
                    $between_tags = substr($message, $start_index + 7, $curr_pos - $start_index - 7);

                    // everything after the [/quote] tag.
                    $after_end_tag = substr($message, $curr_pos + 8);

                    $message = $before_start_tag . xarML('Quote').":<blockquote>";
                    $message .= $between_tags . "</blockquote>";
                    $message .= $after_end_tag;
                    
                    // Now.. we've screwed up the indices by changing the length of the string. 
                    // So, if there's anything in the stack, we want to resume searching just after it.
                    // otherwise, we go back to the start.
                    if (sizeof($stack) > 0)
                    {
                        $curr_pos = array_pop($stack);
                        array_push($stack, $curr_pos);
                        ++$curr_pos;
                    }
                    else
                    {
                        $curr_pos = 1;
                    }
                }
                else
                {
                    // No matching start tag found. Increment pos, keep going.
                    ++$curr_pos;    
                }
            }
            else
            {
                // No starting tag or ending tag.. Increment pos, keep looping.,
                ++$curr_pos;    
            }
        }
    } // while
    
    return $message;
    
} // bbcode_encode_quote()


/**
 * Nathan Codding - Jan. 12, 2001.
 * Performs [code][/code] bbencoding on the given string, and returns the results.
 * Any unmatched "[code]" or "[/code]" token will just be left alone. 
 * This works fine with both having more than one code block in a message, and with nested code blocks.
 * Since that is not a regular language, this is actually a PDA and uses a stack. Great fun.
 *
 * Note: This function assumes the first character of $message is a space, which is added by 
 * bbencode().
 */
function bbcode_encode_code($message, $is_html_disabled)
{
    // First things first: If there aren't any "[code]" strings in the message, we don't
    // need to process it at all.
    if (!strpos(strtolower($message), "[code]"))
    {
        return $message;    
    }
    
    // Second things second: we have to watch out for stuff like [1code] or [/code1] in the 
    // input.. So escape them to [#1code] or [/code#1] for now:
    $message = preg_replace("/\[([0-9]+?)code\]/si", "[#\\1code]", $message);
    $message = preg_replace("/\[\/code([0-9]+?)\]/si", "[/code#\\1]", $message);
    
    $stack = Array();
    $curr_pos = 1;
    $max_nesting_depth = 0;
    while ($curr_pos && ($curr_pos < strlen($message)))
    {    
        $curr_pos = strpos($message, "[", $curr_pos);
    
        // If not found, $curr_pos will be 0, and the loop will end.
        if ($curr_pos)
        {
            // We found a [. It starts at $curr_pos.
            // check if it's a starting or ending code tag.
            $possible_start = substr($message, $curr_pos, 6);
            $possible_end = substr($message, $curr_pos, 7);
            if (strcasecmp("[code]", $possible_start) == 0)
            {
                // We have a starting code tag.
                // Push its position on to the stack, and then keep going to the right.
                array_push($stack, $curr_pos);
                ++$curr_pos;
            }
            else if (strcasecmp("[/code]", $possible_end) == 0)
            {
                // We have an ending code tag.
                // Check if we've already found a matching starting tag.
                if (sizeof($stack) > 0)
                {
                    // There exists a starting tag. 
                    $curr_nesting_depth = sizeof($stack);
                    $max_nesting_depth = ($curr_nesting_depth > $max_nesting_depth) ? $curr_nesting_depth : $max_nesting_depth;
                    
                    // We need to do 2 replacements now.
                    $start_index = array_pop($stack);

                    // everything before the [code] tag.
                    $before_start_tag = substr($message, 0, $start_index);

                    // everything after the [code] tag, but before the [/code] tag.
                    $between_tags = substr($message, $start_index + 6, $curr_pos - $start_index - 6);

                    // everything after the [/code] tag.
                    $after_end_tag = substr($message, $curr_pos + 7);

                    $message = $before_start_tag . "[" . $curr_nesting_depth . "code]";
                    $message .= $between_tags . "[/code" . $curr_nesting_depth . "]";
                    $message .= $after_end_tag;
                    
                    // Now.. we've screwed up the indices by changing the length of the string. 
                    // So, if there's anything in the stack, we want to resume searching just after it.
                    // otherwise, we go back to the start.
                    if (sizeof($stack) > 0)
                    {
                        $curr_pos = array_pop($stack);
                        array_push($stack, $curr_pos);
                        ++$curr_pos;
                    }
                    else
                    {
                        $curr_pos = 1;
                    }
                }
                else
                {
                    // No matching start tag found. Increment pos, keep going.
                    ++$curr_pos;    
                }
            }
            else
            {
                // No starting tag or ending tag.. Increment pos, keep looping.,
                ++$curr_pos;    
            }
        }
    } // while
    
    if ($max_nesting_depth > 0)
    {
        for ($i = 1; $i <= $max_nesting_depth; ++$i)
        {
            $start_tag = escape_slashes(preg_quote("[" . $i . "code]"));
            $end_tag = escape_slashes(preg_quote("[/code" . $i . "]"));
            
            $match_count = preg_match_all("/$start_tag(.*?)$end_tag/si", $message, $matches);
    
            for ($j = 0; $j < $match_count; $j++)
            {
                $before_replace = escape_slashes(preg_quote($matches[1][$j]));
                $after_replace = $matches[1][$j];
                
                if (($i < 2) && !$is_html_disabled)
                {
                    // don't escape special chars when we're nested, 'cause it was already done
                    // at the lower level..
                    // also, don't escape them if HTML is disabled in this post. it'll already be done
                    // by the posting routines.
                    $after_replace = htmlspecialchars($after_replace);    
                }
                
                $str_to_match = $start_tag . $before_replace . $end_tag;

                if (phpversion() > "4.2.0"){
                    highlight_string($after_replace, TRUE);
                }
                
                $message = preg_replace("/$str_to_match/si", xarML('Code') . ": <blockquote><pre> " . bbcode_br2nl($after_replace) . "</pre></blockquote>", $message);
            }
        }
    }
    
    // Undo our escaping from "second things second" above..
    $message = preg_replace("/\[#([0-9]+?)code\]/si", "[\\1code]", $message);
    $message = preg_replace("/\[\/code#([0-9]+?)\]/si", "[/code\\1]", $message);
    return $message;
    
} // bbcode_encode_code()


/**
 * Nathan Codding - Jan. 12, 2001.
 * Performs [list][/list] and [list=?][/list] bbencoding on the given string, and returns the results.
 * Any unmatched "[list]" or "[/list]" token will just be left alone. 
 * This works fine with both having more than one list in a message, and with nested lists.
 * Since that is not a regular language, this is actually a PDA and uses a stack. Great fun.
 *
 * Note: This function assumes the first character of $message is a space, which is added by 
 * bbencode().
 */
function bbcode_encode_list($message)
{        
    $start_length = Array();
    $start_length['ordered'] = 8;
    $start_length['unordered'] = 6;
    
    // First things first: If there aren't any "[list" strings in the message, we don't
    // need to process it at all.
    
    if (!strpos(strtolower($message), "[list"))
    {
        return $message;    
    }
    
    $stack = Array();
    $curr_pos = 1;
    while ($curr_pos && ($curr_pos < strlen($message)))
    {    
        $curr_pos = strpos($message, "[", $curr_pos);
    
        // If not found, $curr_pos will be 0, and the loop will end.
        if ($curr_pos)
        {
            // We found a [. It starts at $curr_pos.
            // check if it's a starting or ending list tag.
            $possible_ordered_start = substr($message, $curr_pos, $start_length['ordered']);
            $possible_unordered_start = substr($message, $curr_pos, $start_length['unordered']);
            $possible_end = substr($message, $curr_pos, 7);
            if (strcasecmp("[list]", $possible_unordered_start) == 0)
            {
                // We have a starting unordered list tag.
                // Push its position on to the stack, and then keep going to the right.
                array_push($stack, array($curr_pos, ""));
                ++$curr_pos;
            }
            else if (preg_match("/\[list=([a1])\]/si", $possible_ordered_start, $matches))
            {
                // We have a starting ordered list tag.
                // Push its position on to the stack, and the starting char onto the start
                // char stack, the keep going to the right.
                array_push($stack, array($curr_pos, $matches[1]));
                ++$curr_pos;
            }
            else if (strcasecmp("[/list]", $possible_end) == 0)
            {
                // We have an ending list tag.
                // Check if we've already found a matching starting tag.
                if (sizeof($stack) > 0)
                {
                    // There exists a starting tag. 
                    // We need to do 2 replacements now.
                    $start = array_pop($stack);
                    $start_index = $start[0];
                    $start_char = $start[1];
                    $is_ordered = ($start_char != "");
                    $start_tag_length = ($is_ordered) ? $start_length['ordered'] : $start_length['unordered'];
                    
                    // everything before the [list] tag.
                    $before_start_tag = substr($message, 0, $start_index);

                    // everything after the [list] tag, but before the [/list] tag.
                    $between_tags = substr($message, $start_index + $start_tag_length, $curr_pos - $start_index - $start_tag_length);
                    
                    //$between_tags = str_replace("[*]", "<li>", $between_tags);

                    // Need to replace [*] with <li> inside the list.
                    $between_tags = preg_replace("/\[li\](.*?)\[\/li\]/si", "<li>\\1</li>", $between_tags);
                   
                    // everything after the [/list] tag.
                    $after_end_tag = substr($message, $curr_pos + 7);

                    if ($is_ordered)
                    {
                        $message = $before_start_tag . "<ol type=" . $start_char . ">";
                        $message .= $between_tags . "</ol>";
                    }
                    else
                    {
                        $message = $before_start_tag . "<ul>";
                        $message .= $between_tags . "</ul>";
                    }
                    
                    $message .= $after_end_tag;
                    
                    // Now.. we've screwed up the indices by changing the length of the string. 
                    // So, if there's anything in the stack, we want to resume searching just after it.
                    // otherwise, we go back to the start.
                    if (sizeof($stack) > 0)
                    {
                        $a = array_pop($stack);
                        $curr_pos = $a[0];
                        array_push($stack, $a);
                        ++$curr_pos;
                    }
                    else
                    {
                        $curr_pos = 1;
                    }
                }
                else
                {
                    // No matching start tag found. Increment pos, keep going.
                    ++$curr_pos;    
                }
            }
            else
            {
                // No starting tag or ending tag.. Increment pos, keep looping.,
                ++$curr_pos;    
            }
        }
    } // while
    
    return $message;
    
} // bbcode_encode_list()



/**
 * Nathan Codding - Oct. 30, 2000
 *
 * Escapes the "/" character with "\/". This is useful when you need
 * to stick a runtime string into a PREG regexp that is being delimited 
 * with slashes.
 */
function escape_slashes($input)
{
    $output = str_replace('/', '\/', $input);
    return $output;
}

/**
 * larsneo - Jan. 11, 2003
 *
 * removes instances of <br /> since sometimes they are stored in DB :(
 */
function bbcode_br2nl($str) {
    return preg_replace("=<br( />|([\s/][^>]*)>)\r?\n?=i", "\n", $str);
}
?>