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
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter Count in #(3), #(1)api_#(2)', 'user', 'transform', 'bbcode');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
        $result = bbcode_transform($extrainfo);
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

    // Change newlines to <br />'s
    $dotransform = xarModGetVar('bbcode', 'dolinebreak');
    if ($dotransform == 1){
        $transformtype = xarModGetVar('bbcode', 'transformtype');
        if ($transformtype == 1){
            $message = str_replace("\n", "<br />", $message);
        } elseif ($transformtype == 2){
            $message = nl2p($message);
            $message = br2p($message);
        }
    }

    // BBClick functionality

    // matches an "xxxx://yyyy" URL at the start of a line, or after a space. 
    // xxxx can only be alpha characters. 
    // yyyy is anything up to the first space, newline, comma, double quote or < 
    $message = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $message); 

    // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing 
    // Must contain at least 2 dots. xxxx contains either alphanum, or "-" 
    // zzzz is optional.. will contain everything up to the first space, newline, 
    // comma, double quote or <. 
    $message = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $message); 

    // matches an email@domain type address at the start of a line, or after a space.
    // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
    $message = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $message);

    // BBCode functionality

    if (strpos($message, "[") && strpos($message, "]")){

        $advancedbbcode = xarModGetVar('bbcode', 'useadvanced');
        if ($advancedbbcode) {

            $codes = xarModAPIFunc('bbcode',
                                   'user',
                                   'getall');

            foreach ($codes as $code) {
                if ((strpos(strtolower($message), $code['tag'])) or (preg_match($code['tag'], $message))) {
                    $message = xarModAPIFunc('bbcode',
                                             'user',
                                             $code['name'],
                                             array('message' => $message));
                }
            }

        } else {

            // [quote]text[/quote] code..
            $patterns[0] = "#\[quote\](.*?)\[/quote\]#si";
            $replacements[0] = "<p>" . xarML('Quote') . " :</p> <div style=\"width: 90%; overflow: auto;\"><blockquote>\\1</blockquote></div>";
            
            // [quote=name]text[/quote] code..
            $patterns[1] = "#\[quote=(.*?)\](.*?)\[/quote\]#si";
            $replacements[1] = "<p>" . xarML('Quote') . " \\1:</p> <div style=\"width: 90%; overflow: auto;\"><blockquote>\\2</blockquote></div>";

            $message = preg_replace($patterns, $replacements, $message);

            // [code] and [/code] for code stuff.
            $message = preg_replace("/\[code\](.*?)\[\/code\]/si", "<p>" . xarML('Code') . ": </p><div class='bbcode_code' style=' padding: 5px; white-space: normal'>\\1</div>", $message);

            // [p] and [/p] for paragraphs.  Bug 3994
            $message = preg_replace("/\[p\](.*?)\[\/p\]/si", "<p>\\1</p>", $message);

            // [u] and [/u] for underline text.
            $message = preg_replace("/\[u\](.*?)\[\/u\]/si", "<span style='text-decoration: underline;'>\\1</span>", $message);

            // [b] and [/b] for bolding text.
            $message = preg_replace("/\[b\](.*?)\[\/b\]/si", "<span style='font-weight: bold;'>\\1</span>", $message);

            // [i] and [/i] for italicizing text.
            $message = preg_replace("/\[i\](.*?)\[\/i\]/si", "<span style='font-style: italic;'>\\1</span>", $message);

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

            $message = preg_replace($patterns, $replacements, $message);

        }

    }

    $message = preg_replace_callback(
       '#\[notransform\](.*?)\[/notransform\]#si',
       create_function(
           // single quotes are essential here,
           // or alternative escape all $ as \$
           '$code',
           'return $code[0] = str_replace(\'<br />\', \'\', $code[0]);'
       ),
       $message
    );

    $message = preg_replace("#\[notransform\](.*?)\[/notransform\]#si", '\\1', $message);


    // Remove our padding from the string..
    $message = substr($message, 1);
    //$test = var_export($message); return "<pre>$test</pre>";
    return $message;
} 

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
* replacement for php's nl2br tag that produces more designer friendly html
*
* Modified from: http://www.php-editors.com/contest/1/51-read.html
*
* @param string $text
* @param string $cssClass
* @return string
*/
function nl2p($text, $cssClass='')
{

 // Return if there are no line breaks.
 if (!strstr($text, "\n")) {
     return $text;
 }

 // Add Optional css class
 if (!empty($cssClass)) {
     $cssClass = ' class="' . $cssClass . '" ';
 }

 // put all text into <p> tags
 $text = '<p' . $cssClass . '>' . $text . '</p>';

 // replace all newline characters with paragraph
 // ending and starting tags
 $text = str_replace("\n", "</p>\n<p" . $cssClass . '>', $text);

 // remove empty paragraph tags & any cariage return characters
 $text = str_replace(array('<p' . $cssClass . '></p>', '<p></p>', "\r"), '', $text);

 return $text;

} // end nl2p

  /**
   * expanding on the nl2p tag above to convert user contributed
   * <br />'s to <p>'s so it displays more nicely.
   *
   * @param string $text
   * @param string $cssClass
   * @return string
   */
function br2p($text, $cssClass='')
{

    if (!eregi('<br', $text)) {
     return $text;
    }

    if (!empty($cssClass)) {
     $cssClass = ' class="' . $cssClass . '" ';
    }

    // put all text into <p> tags
    $text = '<p' . $cssClass . '>' . $text . '</p>';

    // replace all break tags with paragraph
    // ending and starting tags
    $text = str_replace(array('<br>', '<br />', '<BR>', '<BR />'), "</p>\n<p" . $cssClass . '>', $text);

    // remove empty paragraph tags
    $text = str_replace(array('<p' . $cssClass . '></p>', '<p></p>', "<p>\n</p>"), '', $text);

    return $text;
}

?>
