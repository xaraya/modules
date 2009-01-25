<?php
/**
 * Wiki
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Wiki User API
 * @author Jim McDonald
 */
/**
 * @todo check if these functions are used, what are they for?
 */

if (!defined('WIKI_ZERO_DEPTH')) {
    define('WIKI_ZERO_DEPTH', 0);
    define('WIKI_SINGLE_DEPTH', 1);
    define('WIKI_ZERO_LEVEL', 0);
    define('WIKI_NESTED_LEVEL', 1);
}

function XARParseAndLink($bracketlink)
{
    static $XARAllowedProtocols;
    if (!isset($XARAllowedProtocols)) {
        $XARAllowedProtocols = xarModGetVar('wiki', 'AllowedProtocols');
    }
    static $XARInlineImages;
    if (!isset($XARInlineImages)) {
        $XARInlineImages = xarModGetVar('wiki', 'InlineImages');
    }

    static $XARlExtlinkNewWindow;
    if (!isset($XARlExtlinkNewWindow)) {
        $XARlExtlinkNewWindow = xarModGetVar('wiki', 'ExtlinkNewWindow');
    }
    static $XARlIntlinkNewWindow;
    if (!isset($XARlIntlinkNewWindow)) {
        $XARlIntlinkNewWindow = xarModGetVar('wiki', 'IntlinkNewWindow');
    }
    // $bracketlink will start and end with brackets; in between
    // will be either a page name, a URL or both separated by a pipe.
    // ou bien du texte, si bbcode.
    // strip brackets and leading space
    preg_match("/(\[\s*)(.+?)(\s*\])/", $bracketlink, $match);

    if (isset($match[3]) and ($match[3] == ']') and (($match[2] == 'b') or ($match[2] == 'i') or ($match[2] == '/b') or ($match[2] == '/i'))) {
        $link['type'] = "bbcode";
        $link['link'] = "<" . $match[2] . ">";
    } else {
        // match the contents
        preg_match("/([^|]+)(\|)?([^|]+)?/", $match[2], $matches);

        if (isset($matches[3])) {
            // named link of the form  "[some link name | http://blippy.com/]"
            $URL = trim($matches[3]);
            $linkname = trim($matches[1]);
            $linktype = 'named';
        } else {
            // unnamed link of the form "[http://blippy.com/] or [wiki page]"
            $URL = trim($matches[1]);
            $linkname = '';
            $linktype = 'simple';
        }
        if (preg_match("#^($XARAllowedProtocols):#", $URL)) {
            // if it's an image, embed it; otherwise, it's a regular link
            if (preg_match("/($XARInlineImages)$/i", $URL)) {
                $link['type'] = "image-$linktype";
                $link['link'] = XARLinkImage($URL, $linkname);
            } else {
                $link['type'] = "url-$linktype";
                $link['link'] = XARLinkURL($URL, $linkname, $XARlExtlinkNewWindow);
            }
        } elseif (preg_match("#^picture:(.*)#", $URL, $match)) {
            $link['type'] = "image-$linktype";
            $link['link'] = XARLinkImage("\"$match[1]\"", $linkname);
        } elseif (preg_match("#^photo:(.*)#", $URL, $match)) {
            $link['type'] = "image-$linktype";
            $link['link'] = XARLinkImage("\"$match[1]\"", $linkname);
        } elseif (preg_match("#^phpwiki:(.*)#", $URL, $match)) {
            $link['type'] = "url-wiki-$linktype";
            if (empty($linkname)) {
                $linkname = $URL;
            }
            $link['link'] = "<a href=\"$match[1]\">$linkname</a>";
        } elseif (preg_match("#^\d+$#", $URL)) {
            $link['type'] = "reference-$linktype";
            $link['link'] = $URL;
        } else {
            $link['type'] = "url-$linktype";
            $link['link'] = XARLinkURL($URL, $linkname, $XARlIntlinkNewWindow);
        }
    }
    return $link;
}

function XARwikiTokenize($str, $pattern, &$orig, &$ntokens)
{
    static $XARFieldSeparator;
    if (!isset($XARFieldSeparator)) {
        $XARFieldSeparator = xarModGetVar('wiki', 'FieldSeparator');
    }
    // Find any strings in $str that match $pattern and
    // store them in $orig, replacing them with tokens
    // starting at number $ntokens - returns tokenized string
    $new = '';
    while (preg_match("/^(.*?)($pattern)/", $str, $matches)) {
        $linktoken = $XARFieldSeparator . $XARFieldSeparator . ($ntokens++) . $XARFieldSeparator;
        $new .= $matches[1] . $linktoken;
        $orig[] = $matches[2];
        $str = substr($str, strlen($matches[0]));
    }
    $new .= $str;
    return $new;
}

function XARwikiInclude($retour)
{
    $retour = preg_replace("|(%%%%)(.*?)(%%%%)|", "\\2", $retour);
    $retour = transform($retour, $this->typeCoding);
    $retour = "<table border=\"0\" cellpadding=\"8\" cellspacing=\"1\" width=\"100%\"><tr><td align=\"left\">" . $retour . "</td></tr></table>";
    return $retour;
}

/**
 * transform text
 *
 * @param array $args['objectid'] string or array of text items
 * @param array $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function wiki_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);

    static $XARAllowedProtocols;
    if (!isset($XARAllowedProtocols)) {
        $XARAllowedProtocols = xarModGetVar('wiki', 'AllowedProtocols');
    }
    // Argument check
    if (!isset($objectid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'objectid', 'userapi', 'transform', 'wiki');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
                     'extrainfo', 'userapi', 'transform', 'wiki');
        xarErrorSet(XAR_USER_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    // What does qualify as wiki content
    // FIXME: This regexp is a nice attemp to quality the wiki format, but it fails
    // silently without telling the user anything at all, rethink this. For now
    // let the regexp just match anything
//  $regexp = "/('''|\t+\*|\t+1|\t+\s:|---|__|(\[[\w ]+\|$XARAllowedProtocols))/";
//  $regexp ="/.*/";
    // Hb: Matching anything can change content even in URL data fields, similar to
    // bug 5960. So the deal here is to decide if the particular string is parsed
    // at all. A useful regexp is "/\n/" for data to contain at least one newline.
    $regexp ="/./";
    if (is_array($extrainfo)) {
        // if extrainfo['transform'] contains the stuff, transform that
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key]) && preg_match($regexp, $extrainfo[$key])) {
                    // Put the transformed stuff back into the extrainfo array
                    $extrainfo[$key] = wiki_userapitransform($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }

        // Otherwise transform the extrainfo array itself
        $transformed = array();
        foreach($extrainfo as $text) {
            if (preg_match($regexp, $text)) {
                $transformed[] = wiki_userapitransform($text);
            } else {
                // Didnt qualify, return verbatim
                $transformed[] = $text;
            }
        }
    } else {
        // if extrainfo is no array, transform it directly
        $transformed = $extrainfo; // default, return verbatim
        if (preg_match($regexp, $extrainfo)) {
            $transformed = wiki_userapitransform($extrainfo);
        }
    }
    return $transformed;
}

/**
 * do the transform from Wiki to HTML
 *
 * @param  $cContent Wiki content
 * @returns string
 * @returns transformed text
 */
function wiki_userapitransform($cContent)
{
    static $XARFieldSeparator;
    if (!isset($XARFieldSeparator)) {
        $XARFieldSeparator = xarModGetVar('wiki', 'FieldSeparator');
    }
    static $XARlWithHtml;
    if (!isset($XARWithHtml)) {
        $XARWithHtml = xarModGetVar('wiki', 'WithHTML');
    }
    static $XARAllowedProtocols;
    if (!isset($XARAllowedProtocols)) {
        $XARAllowedProtocols = xarModGetVar('wiki', 'AllowedProtocols');
    }

    $html = "";

    if (strlen($cContent) == 0) {
        return($cContent);
    }

    $aContent = explode("\n", $cContent);

    $aContent = XARCookSpaces($aContent);
    // Loop over all lines of the page and apply transformation rules
    $numlines = count($aContent);
    for ($index = 0; $index < $numlines; $index++) {
        unset($tokens);
        unset($replacements);
        $ntokens = 0;
        $replacements = array();
        // $tmpline = stripslashes($aContent[$index]);
        $tmpline = $aContent[$index];

        if (!strlen($tmpline) || $tmpline == "\r") {
            // this is a blank line, send <p>
            $html .= XARSetHTMLOutputMode("p", WIKI_ZERO_DEPTH, 0);
            continue;
        } elseif ($XARlWithHtml and preg_match("/(^\|)(.*)/", $tmpline, $matches)) {
            // HTML mode
            $html .= XARSetHTMLOutputMode("", WIKI_ZERO_LEVEL, 0);
            $html .= $matches[2];
            continue;
        }
        // ////////////////////////////////////////////////////////
        // New linking scheme: links are in brackets. This will
        // emulate typical HTML linking as well as Wiki linking.
        // First need to protect [[.
        $oldn = $ntokens;
        $tmpline = XARwikiTokenize($tmpline, '\[\[', $replacements, $ntokens);
        while ($oldn < $ntokens)
        $replacements[$oldn++] = '[';
        // Now process the [\d+] links which are numeric references
        $oldn = $ntokens;
        $tmpline = XARwikiTokenize($tmpline, '\[\s*\d+\s*\]', $replacements, $ntokens);
        while ($oldn < $ntokens) {
            $num = (int) substr($replacements[$oldn], 1);
            if (! empty($embedded[$num]))
                $replacements[$oldn] = $embedded[$num];
            $oldn++;
        }
        // match anything else between brackets
        $oldn = $ntokens;
        $tmpline = XARwikiTokenize($tmpline, '\[.+?\]', $replacements, $ntokens);
        while ($oldn < $ntokens) {
            $link = XARParseAndLink($replacements[$oldn]);
            $replacements[$oldn] = $link['link'];
            $oldn++;
        }
        // ////////////////////////////////////////////////////////
        // replace all URL's with tokens, so we don't confuse them
        // with Wiki words later. Wiki words in URL's break things.
        // URLs preceeded by a '!' are not linked
        $tmpline = XARwikiTokenize($tmpline, "!?\b($XARAllowedProtocols):[^\s<>\[\]\"'()]*[^\s<>\[\]\"'(),.?]", $replacements, $ntokens);

        while ($oldn < $ntokens) {
            if ($replacements[$oldn][0] == '!')
                $replacements[$oldn] = substr($replacements[$oldn], 1);
            else
                $replacements[$oldn] = XARLinkURL($replacements[$oldn]);
            $oldn++;
        }
        // ////////////////////////////////////////////////////////
        // escape HTML metachars
        // $tmpline = str_replace('&', '&amp;', $tmpline);
        // $tmpline = str_replace('>', '&gt;', $tmpline);
        // $tmpline = str_replace('<', '&lt;', $tmpline);
        // four or more dashes to <hr/>
        $tmpline = ereg_replace("^-{4,}",
            '<hr/>',
            $tmpline);
        // %%%% are image blocks
        if (preg_match("|(%%%%)(.*?)(%%%%)|", $tmpline, $aContenu)) {
            $retour = XARwikiInclude($aContenu[0]);
            $retour = preg_replace("|(%%%%)(.*?)(%%%%)|", "\\2", $retour);
            $retour = wiki_userapitransform($retour);
            $retour = "<table border=\"0\" cellpadding=\"8\" cellspacing=\"1\" width=\"100%\"><tr><td align=\"left\">" . $retour . "</td></tr></table>";
            $tmpline = $retour . preg_replace("|(%%%%)(.*?)(%%%%)|",
                "",
                $tmpline);
        }
        // %%% are linebreaks
        if (strstr($tmpline, '%%%')) {
            // i dont want ' %%%' or '%%% '
            str_replace("%%% ", "%%%", $tmpline);
            str_replace(" %%%", "%%%", $tmpline);
            // i want to check i dont have '%%%<br />'
            str_replace("%%%<br />", "%%%", $tmpline);
            $tmpline = str_replace('%%%',
                '<br />',
                $tmpline);
        }
        // bold italics (old way)
        $tmpline = preg_replace("|(''''')(.*?)(''''')|",
            "<strong><em>\\2</em></strong>",
            $tmpline);
        // bold (old way)
        $tmpline = preg_replace("|(''')(.*?)(''')|",
            "<strong>\\2</strong>",
            $tmpline);
        // italics (old ways)
        $tmpline = preg_replace("|('')(.*?)('')|",
            "<em>\\2</em>",
            $tmpline);
        // bold
        $tmpline = preg_replace("|(___)(.*?)(___)|",
            "<strong>\\2</strong>",
            $tmpline);
        // italics
        $tmpline = preg_replace("|(__)(.*?)(__)|",
            "<em>\\2</em>",
            $tmpline);
        // bold italics
        $tmpline = preg_replace("|(_____)(.*?)(_____)|",
            "<strong><em>\\2</em></strong>",
            $tmpline);
        // center
        $tmpline = preg_replace("|(---)(.*?)(---)|",
            "<center>\\2</center>",
            $tmpline);
        // tag <PUB>
        // $tmpline = str_replace("<PUB>",  impHtml() , $tmpline );
        // ////////////////////////////////////////////////////////
        // unordered, ordered, and dictionary list  (using TAB)
        if (preg_match("/(^\t+)(.*?)(:\t)(.*$)/", $tmpline, $matches)) {
            // this is a dictionary list (<dl>) item
            $numtabs = strlen($matches[1]);
            $html .= XARSetHTMLOutputMode('dl', WIKI_NESTED_LEVEL, $numtabs);
            $tmpline = '';
            if (trim($matches[2]))
                $tmpline = '<dt>' . $matches[2];
            $tmpline .= '<dd>' . $matches[4];
        } elseif (preg_match("/(^\t+)(\*|\d+|#)/", $tmpline, $matches)) {
            // this is part of a list (<ul>, <ol>)
            $numtabs = strlen($matches[1]);
            if ($matches[2] == '*') {
                $listtag = 'ul';
            } else {
                $listtag = 'ol'; // a rather tacit assumption. oh well.
            }
            $tmpline = preg_replace("/^(\t+)(\*|\d+|#)/", "", $tmpline);
            $html .= XARSetHTMLOutputMode($listtag, WIKI_NESTED_LEVEL, $numtabs);
            $html .= '<li>';
            // ////////////////////////////////////////////////////////
            // tabless markup for unordered, ordered, and dictionary lists
            // ul/ol list types can be mixed, so we only look at the last
            // character. Changes e.g. from "**#*" to "###*" go unnoticed.
            // and wouldn't make a difference to the HTML layout anyway.
            // unordered lists <UL>: "*"
        } elseif (preg_match("/^([#*]*\*)[^#]/", $tmpline, $matches)) {
            // this is part of an unordered list
            $numtabs = strlen($matches[1]);
            $tmpline = preg_replace("/^([#*]*\*)/", '', $tmpline);
            $html .= XARSetHTMLOutputMode('ul', WIKI_NESTED_LEVEL, $numtabs);
            $html .= '<li>';
            // ordered lists <OL>: "#"
        } elseif (preg_match("/^([#*]*\#)/", $tmpline, $matches)) {
            // this is part of an ordered list
            $numtabs = strlen($matches[1]);
            $tmpline = preg_replace("/^([#*]*\#)/", "", $tmpline);
            $html .= XARSetHTMLOutputMode('ol', WIKI_NESTED_LEVEL, $numtabs);
            $html .= '<li>';
            // definition lists <DL>: ";text:text"
        } elseif (preg_match("/(^;+)(.*?):(.*$)/", $tmpline, $matches)) {
            // this is a dictionary list item
            $numtabs = strlen($matches[1]);
            $html .= XARSetHTMLOutputMode('dl', WIKI_NESTED_LEVEL, $numtabs);
            $tmpline = '';
            if (trim($matches[2]))
                $tmpline = '<dt>' . $matches[2];
            $tmpline .= '<dd>' . $matches[3];
            // ////////////////////////////////////////////////////////
            // remaining modes: preformatted text, headings, normal text
            // preformated mode was a pb. So ...
            // } elseif (preg_match("/^\s+/", $tmpline)) {
            // this is preformatted text, i.e. <pre>
            // $html .= "????";
            // $html .= XARSetHTMLOutputMode('pre', WIKI_ZERO_LEVEL, 0);
        } elseif (preg_match("/^(!{1,3})[^!]/", $tmpline, $whichheading)) {
            // lines starting with !,!!,!!! are headings
            if ($whichheading[1] == '!') $heading = 'h3';
            elseif ($whichheading[1] == '!!') $heading = 'h2';
            elseif ($whichheading[1] == '!!!') $heading = 'h1';
            $tmpline = preg_replace("/^!+/", '', $tmpline);
            $html .= XARSetHTMLOutputMode($heading, WIKI_ZERO_LEVEL, 0);
        } else {
            // it's ordinary output if nothing else
            $html .= XARSetHTMLOutputMode('', WIKI_ZERO_LEVEL, 0);
        }
        // /////////////////////////////////////////////////////
        // Replace tokens
        for ($i = 0; $i < $ntokens; $i++)
        $tmpline = str_replace($XARFieldSeparator . $XARFieldSeparator . $i . $XARFieldSeparator, $replacements[$i], $tmpline);

        $html .= $tmpline . "\n";
    }
    $html .= XARSetHTMLOutputMode('', WIKI_ZERO_LEVEL, 0);

    return $html;
}

function XARLinkURL($url, $linktext = '', $autreFenetre = false)
{
    if (ereg("[<>\"]", $url)) {
        return "<b><u>BAD URL -- remove all of &lt;, &gt;, &quot;</u></b>";
    }
    if (empty($linktext))
        $linktext = $url;
    if ($autreFenetre) {
        $target = " target=\"sb\"";
    } else {
        $target = "";
    }
    return "<a href=\"$url\"" . $target . ">$linktext</a>";
}

function XARLinkImage($url, $alt = '')
{
    static $XARlExtlinkNewWindow;
    if (!isset($XARlExtlinkNewWindow)) {
        $XARlExtlinkNewWindow = xarModGetVar('wiki', 'ExtlinkNewWindow');
    }
    static $XARlIntlinkNewWindow;
    if (!isset($XARlIntlinkNewWindow)) {
        $XARlIntlinkNewWindow = xarModGetVar('wiki', 'IntlinkNewWindow');
    }

    if (ereg('[<>]', $url)) {
        return "<b><u>BAD URL -- remove all of &lt;, &gt;, &quot;</u></b>";
    }
    $link = '';

    $chaine = substr ($alt, 0, strpos($alt, '+'));
    if (!(empty($chaine))) {
        $link = substr ($alt, strpos($alt, '+') + 1);
        $alt = substr ($alt, 0, strpos($alt, '+'));
    }

    $cRetour = "\n";
    $cRetour .= "<!-- inclusion de la photo de l'article. -->\n";
    $cRetour .= "<table cellspacing=\"0\" cellpadding=\"5\" border=\"0\" align=\"left\">\n";
    $cRetour .= "<tr><td valign=\"top\">\n";

    if (!(empty($link))) {
        $cRetour .= "<a href=" . $link ;
        if (strstr($link, "http://") and $XARlExtlinkNewWindow) {
            $cRetour .= " target='_blank' ";
        } elseif (($XARlIntlinkNewWindow)) {
            $cRetour .= " target='_blank' ";
        }
        $cRetour .= ">" ;
    }
    $cRetour .= "<img src=\"$url\" alt=\"$alt\" border=\"0\" />\n";
    if (!(empty($link))) {
        $cRetour .= "</a>" ;
    }
    $cRetour .= "</td></tr></table>\n";
    $cRetour .= "<!-- fin de l'inclusion de la photo de l'article. -->\n";
    return $cRetour;
}
// converts spaces to tabs
function XARCookSpaces($pagearray)
{
    return preg_replace("/ {3,8}/", "\t", $pagearray);
}

class XARStack
{
    var $items = array();
    var $size = 0;

    function push($item)
    {
        $this->items[$this->size] = $item;
        $this->size++;
        return true;
    }

    function pop()
    {
        if ($this->size == 0) {
            return false; // stack is empty
        }
        $this->size--;
        return $this->items[$this->size];
    }

    function cnt()
    {
        return $this->size;
    }

    function top()
    {
        if ($this->size)
            return $this->items[$this->size - 1];
        else
            return '';
    }
}
// end class definition
// globalize it here cause xarinclude_once.
global $XARstack;
$XARstack = new XARStack;

/*
   Wiki HTML output can, at any given time, be in only one mode.
   It will be something like Unordered List, Preformatted Text,
   plain text etc. When we change modes we have to issue close tags
   for one mode and start tags for another.

   $tag ... HTML tag to insert
   $tagtype ... WIKI_ZERO_LEVEL - close all open tags before inserting $tag
   WIKI_NESTED_LEVEL - close tags until depths match
   $level ... nesting level (depth) of $tag
   nesting is arbitrary limited to 10 levels
 */

function XARSetHTMLOutputMode($tag, $tagtype, $level)
{
    global $XARstack;
    $retvar = '';

    if ($tagtype == WIKI_ZERO_LEVEL) {
        // empty the stack until $level == 0;
        if ($tag == $XARstack->top()) {
            return; // same tag? -> nothing to do
        } while ($XARstack->cnt() > 0) {
            $closetag = $XARstack->pop();
            $retvar .= "</$closetag>\n";
        }

        if ($tag) {
            $retvar .= "<$tag>\n";
            $XARstack->push($tag);
        }
    } elseif ($tagtype == WIKI_NESTED_LEVEL) {
        if ($level < $XARstack->cnt()) {
            // $tag has fewer nestings (old: tabs) than stack,
            // reduce stack to that tab count
            while ($XARstack->cnt() > $level) {
                $closetag = $XARstack->pop();
                if ($closetag == false) {
                    break;
                }
                $retvar .= "</$closetag>\n";
            }
            // if list type isn't the same,
            // back up one more and push new tag
            if ($tag != $XARstack->top()) {
                $closetag = $XARstack->pop();
                $retvar .= "</$closetag><$tag>\n";
                $XARstack->push($tag);
            }
        } elseif ($level > $XARstack->cnt()) {
            // we add the diff to the stack
            // stack might be zero
            while ($XARstack->cnt() < $level) {
                $retvar .= "<$tag>\n";
                $XARstack->push($tag);
                if ($XARstack->cnt() > 10) {
                    // arbitrarily limit tag nesting
                    xarSessionSetVar('errormsg', 'Stack bounds exceeded in SetHTMLOutputMode');
                }
            }
        } else { // $level == $XARstack->cnt()
            if ($tag == $XARstack->top()) {
                return; // same tag? -> nothing to do
            } else {
                // different tag - close old one, add new one
                $closetag = $XARstack->pop();
                $retvar .= "</$closetag>\n";
                $retvar .= "<$tag>\n";
                $XARstack->push($tag);
            }
        }
    } else { // unknown $tagtype
        xarSessionSetVar('errormsg', 'Passed bad tag type value in SetHTMLOutputMode');
    }
    return $retvar;
}
?>
