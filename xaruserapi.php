<?php
/**
 * Xaraya BBCode
 *
 * Based on pnBBCode Hook from larsneo
 * http://www.pncommunity.de
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage BBCode Module
 * @link http://xaraya.com/index.php/release/778.html
 * @author John Cox
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

    // BBClick functionality
    // matches an "xxxx://yyyy" URL at the start of a line, or after a space. 
    // xxxx can only be alpha characters. 
    // yyyy is anything up to the first space, newline, comma, double quote [ or < 
    // transform to BBCode, instead of HTML, let the BBCode template deal with display
    // Bug, this doesn't catch plain urls inside other BBCode tags.
    // Added regex to not match string beginning with [url] or [url=] allows all other tags to work
    $text = preg_replace("#(^|[\n ]|[^[url.]])([\w]+?://[^ \"\n\r\t<[]*)#is", "\\1[url]\\2[/url]", $text); 
    //$text = preg_replace("#(^|[\n ])([\w]+?://[^ \"\n\r\t<]*)#is", "\\1<a href=\"\\2\" target=\"_blank\">\\2</a>", $text); 

    // matches a "www|ftp.xxxx.yyyy[/zzzz]" kinda lazy URL thing
    // Must contain at least 2 dots. xxxx contains either alphanum, or "-"
    // zzzz is optional.. will contain everything up to the first space, newline, 
    // comma, double quote [ or <. 
    // transform to BBCode tag, instead of HTML, let the BBCode template deal with display
    $text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<[]*)#is", "\\1[url=http://\\2]\\2[/url]", $text);  
    //$text = preg_replace("#(^|[\n ])((www|ftp)\.[^ \"\t\n\r<]*)#is", "\\1<a href=\"http://\\2\" target=\"_blank\">\\2</a>", $text); 

    // matches an email@domain type address at the start of a line, or after a space.
    // Note: Only the followed chars are valid; alphanums, "-", "_" and or ".".
    // transform to BBCode, instead of HTML, let the BBCode template deal with display
    $text = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1[email]\\2@\\3[/email]", $text);
    //$text = preg_replace("#(^|[\n ])([a-z0-9&\-_.]+?)@([\w\-]+\.([\w\-\.]+\.)*[\w]+)#i", "\\1<a href=\"mailto:\\2@\\3\">\\2@\\3</a>", $text);

    include_once 'modules/bbcode/xarclass/stringparser_bbcode.class.php';
    $bbcode = new StringParser_BBCode();
    // Bug 5777 make bbcode case insensitive
    $bbcode->setGlobalCaseSensitive(false);
    $bbcode->addCode ('p', 'callback_replace', 'do_bbcode_para', array (),
                       'inline', array ('listitem', 'block', 'inline', 'link'), array());
     $bbcode->addCode ('b', 'callback_replace', 'do_bbcode_bold', array (), 
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('i', 'callback_replace', 'do_bbcode_italics', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('u', 'callback_replace', 'do_bbcode_underline', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('dictionary', 'callback_replace', 'do_bbcode_dictionary', array (),
                      'inline', array ('listitem', 'block', 'inline'), array());
    $bbcode->addCode ('email', 'callback_replace', 'do_bbcode_email', array (),
                      'inline', array ('listitem', 'block', 'inline'), array());
    $bbcode->addCode ('google', 'callback_replace', 'do_bbcode_google', array (),
                      'inline', array ('listitem', 'block', 'inline'), array());
    $bbcode->addCode ('msn', 'callback_replace', 'do_bbcode_msn', array (),
                      'inline', array ('listitem', 'block', 'inline'), array());
    $bbcode->addCode ('yahoo', 'callback_replace', 'do_bbcode_yahoo', array (),
                      'inline', array ('listitem', 'block', 'inline'), array());
    //bug 5214 - xbbc-000229                  
    $bbcode->addCode ('wiki', 'usecontent', 'do_bbcode_wiki',  
                        array ('usecontent_param' => 'default'), 
                        'inline', 
                        array ('listitem', 'block', 'inline'), 
                        array()
                        );
    $bbcode->addCode ('thesaurus', 'callback_replace', 'do_bbcode_thesaurus', array (),
                      'inline', array ('listitem', 'block', 'inline'), array());
    $bbcode->addCode ('linethrough', 'callback_replace', 'do_bbcode_linethrough', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('overline', 'callback_replace', 'do_bbcode_overline', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('underline', 'callback_replace', 'do_bbcode_underline', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('smallcaps', 'callback_replace', 'do_bbcode_smallcaps', array (),
                      'inline', array ('block', 'inline'), array());
    $bbcode->addCode ('sup', 'callback_replace', 'do_bbcode_sup', array (),
                      'inline', array ('block', 'inline'), array());
    $bbcode->addCode ('sub', 'callback_replace', 'do_bbcode_sub', array (),
                      'inline', array ('block', 'inline'), array());
    $bbcode->addCode ('url', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
                      'link', array ('block', 'inline'), array ('link'));
    $bbcode->addCode ('link', 'usecontent?', 'do_bbcode_url', array ('usecontent_param' => 'default'),
                      'link', array ('block', 'inline'), array ('link'));
    $bbcode->addCode ('you', 'callback_replace_single', 'do_bbcode_you', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());

    $bbcode->addCode ('code', 'usecontent?', 'do_bbcode_code', array ('usecontent_param' => 'default'), 'link', array ('block', 'inline'), array ('link'));
    // don't let paragraphs split the code
    $bbcode->setCodeFlag ('code', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
    //bug 5217
    //$bbcode->addCode ('quote', 'usecontent?', 'do_bbcode_quote', array ('usecontent_param' => 'default'), 'link', array ('block', 'inline'), array ('link'));
    $bbcode->addCode ('quote', 'callback_replace', 'do_bbcode_quote', array ('usecontent_param' => 'default'), 'block', array ('block', 'inline','link'), array ());
    // don't let paragraphs split the quotes
    $bbcode->setCodeFlag ('quote', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
    $bbcode->addCode ('color', 'usecontent?', 'do_bbcode_color', array ('usecontent_param' => 'default'),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array ('link'));
    //$bbcode->addCode ('img', 'usecontent', 'do_bbcode_img', array (),
    $bbcode->addCode ('img', 'usecontent?', 'do_bbcode_img', array ('usecontent_param' => 'default'),
                       'image', array ('listitem', 'block', 'inline', 'link'), array ());
    $bbcode->addCode ('bild', 'usecontent', 'do_bbcode_img', array (),
                      'image', array ('listitem', 'block', 'inline', 'link'), array ());
    // Bug, nested tags not transformed inside size tag
    $bbcode->addCode ('size', 'callback_replace', 'do_bbcode_size', array ('usecontent_param' => 'default'), 'inline', array ('listitem', 'block','inline', 'link'), array ());
    /*
    $bbcode->addCode ('size', 'usecontent', 'do_bbcode_size', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array ());
    */
    $bbcode->addCode ('arabic', 'callback_replace', 'do_bbcode_rtl', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());
    $bbcode->addCode ('hebrew', 'callback_replace', 'do_bbcode_rtl', array (),
                      'inline', array ('listitem', 'block', 'inline', 'link'), array());

    $bbcode->setOccurrenceType ('img', 'image');
    $bbcode->setOccurrenceType ('bild', 'image');
    $bbcode->setMaxOccurrences ('image', 2);

    $bbcode->addCode ('list', 'simple_replace', null, array ('start_tag' => '<ul>', 'end_tag' => '</ul>'),
                      'list', array ('block', 'listitem'), array ());
    $bbcode->addCode ('*', 'simple_replace', null, array ('start_tag' => '<li>', 'end_tag' => '</li>'),
                      'listitem', array ('list'), array ());
    $bbcode->setCodeFlag ('*', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
    $bbcode->setCodeFlag ('*', 'paragraphs', true);
    $bbcode->setCodeFlag ('list', 'paragraph_type', BBCODE_PARAGRAPH_BLOCK_ELEMENT);
    $bbcode->setCodeFlag ('list', 'opentag.before.newline', BBCODE_NEWLINE_DROP);
    $bbcode->setCodeFlag ('list', 'closetag.before.newline', BBCODE_NEWLINE_DROP);
    // Add img line for bug 5217
    $bbcode->setCodeFlag ('img', 'closetag', BBCODE_CLOSETAG_OPTIONAL);
    $bbcode->addFilter(STRINGPARSER_FILTER_PRE, 'convertlinebreaks');
    //$bbcode->addParser(array ('block', 'inline', 'link', 'listitem'), 'htmlspecialchars');
    $bbcode->addParser ('list', 'bbcode_stripcontents');
    $dotransform = xarModGetVar('bbcode', 'dolinebreak');
    if ($dotransform == 1){
        $bbcode->addParser(array ('block', 'inline', 'link', 'listitem'), 'nl2br');
        $bbcode->setRootParagraphHandling(true);
    }
    $text = $bbcode->parse($text);
    $text = str_replace('<p><blockquote>', '<blockquote><p>', $text);
    $text = str_replace('</blockquote></p>', '</p></blockquote>', $text);
    return $text;
}

// Unify line breaks of different operating systems
function convertlinebreaks ($text) 
{
    return preg_replace ("/\015\012|\015|\012/", "\n", $text);
}
// Remove everything but the newline charachter
function bbcode_stripcontents ($text) 
{
    return preg_replace ("/[^\n]/", '', $text);
}

// Function to include images
function do_bbcode_img ($action, $attributes, $content, $params, $node_object)
{
    if ($action == 'validate') {
        return true;
    }
    // Why isn't this returned to a template like all the others?
    //return '<img src="'.htmlspecialchars($content).'" alt="">';  bug 5217
    if (!isset ($attributes['default'])) {
    		return '<img src="'.htmlspecialchars($content).'" alt="">';
    } else {
    		return '<img src="'.htmlspecialchars($attributes['default']).'" alt="">';
    }
}
// Bug 4826
function do_bbcode_para ($action, $attributes, $content, $params, &$node_object)
{
    return xarTplModule('bbcode','user', 'para', array('replace' => $content));
}
function do_bbcode_bold ($action, $attributes, $content, $params, &$node_object)
{
    return xarTplModule('bbcode','user', 'bold', array('replace' => $content));
}
function do_bbcode_italics ($action, $attributes, $content, $params, &$node_object)
{
    return xarTplModule('bbcode','user', 'italics', array('replace' => $content));
}
function do_bbcode_dictionary ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'dictionary', array('replace' => $content));
}
function do_bbcode_email ($action, $attributes, $content, $params, &$node_object)
{
    return xarTplModule('bbcode','user', 'email', array('replace' => $content));
}
function do_bbcode_google ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'google', array('replace' => $content));
}
function do_bbcode_msn ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'msn', array('replace' => $content));
}
function do_bbcode_wiki ($action, $attributes, $content, $params, &$node_object) 
{
    if (!isset ($attributes['default'])) {
        // if option not specified then link to main site
        return xarTplModule('bbcode','user', 'wiki', array('iso' => 'www', 'replace' => $content));
    } else {
        // only valid with two characters iso code
        $iso = htmlspecialchars(substr($attributes['default'],0,2));
        // if option 'de' or 'ru' etc specified than go to de.wiki.org or ru.wiki.org  etc
        return xarTplModule('bbcode','user', 'wiki', array('iso' => $iso, 'replace' => $content ));
    }
}
function do_bbcode_yahoo ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'yahoo', array('replace' => $content));
}
function do_bbcode_thesaurus ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'thesaurus', array('replace' => $content));
}
function do_bbcode_linethrough ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'linethrough', array('replace' => $content));
}
function do_bbcode_overline ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'overline', array('replace' => $content));
}
function do_bbcode_underline ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'underline', array('replace' => $content));
}
function do_bbcode_smallcaps ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'smallcaps', array('replace' => $content));
}
function do_bbcode_sup ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'sup', array('replace' => $content));
}
function do_bbcode_sub ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'sub', array('replace' => $content));
}
function do_bbcode_you ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'you', array('replace' => $content));
}
function do_bbcode_url ($action, $attributes, $content, $params, &$node_object)
{
    // 1) the code is being valided
    if ($action == 'validate') {
        // the code is specified as follows: [url]http://.../[/url]
        if (!isset ($attributes['default'])) {
            // is this a valid URL?
            return is_valid_url($content);
        }
        // the code is specified as follows: [url=http://.../]Text[/url]
        // is this a valid URL?
        return is_valid_url($attributes['default']);
    } else {
        // the code was specified as follows: [url]http://.../[/url]
        if (!isset ($attributes['default'])) {
            return xarTplModule('bbcode','user', 'url', array('url' => $content));
        } else {
            return xarTplModule('bbcode','user', 'url', array('url' => $attributes['default'], 'name' => $content ));
        }
    }
}

function is_valid_url($url)
{
    $parsed_url = parse_url($url);
    if (!isset($parsed_url['scheme'])){
        return false;
    } else {
        return true;
    }
}

function do_bbcode_color ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'color', array('color' => $attributes['default'], 'content' => $content ));
}
function do_bbcode_size ($action, $attributes, $content, $params, &$node_object) 
{
    return xarTplModule('bbcode','user', 'size', array('size' => $attributes['default'], 'content' => $content ));
}
function do_bbcode_quote ($action, $attributes, $content, $params, &$node_object) 
{
    if (!isset ($attributes['default'])) {
        return xarTplModule('bbcode','user', 'quote', array('quote' => $content));
    } else {
        return xarTplModule('bbcode','user', 'quote', array('who' => $attributes['default'], 'quote' => $content ));
    }
}

function do_bbcode_code ($action, $attributes, $content, $params, &$node_object) 
{
    // flag tags to be ignored by other modules handling linebreaks (ie HTML)
    if (xarModGetVar('bbcode', 'dolinebreak') == 1) {
      static $nolinebreaks = array();
      if (!isset ($attributes['default'])) {
          $nolinebreaks['code'] = 1;
      } else {
          $nolinebreaks['textarea'] = 1;
      }
      xarVarSetCached('Hooks.bbcode', 'nolinebreaks', join(';', array_keys($nolinebreaks)));
    }

    if (!isset ($attributes['default'])) {
        return xarTplModule('bbcode','user', 'code', array('replace' => $content));
    } elseif ($attributes['default'] == 'php') {
        return xarTplModule('bbcode','user', 'phpcode', array('replace' => $content));
    } elseif ($attributes['default'] == 'jscript') {
        return xarTplModule('bbcode','user', 'jscriptcode', array('replace' => $content));
    } elseif ($attributes['default'] == 'sql') {
        return xarTplModule('bbcode','user', 'sqlcode', array('replace' => $content));
    } elseif ($attributes['default'] == 'xml') {
        return xarTplModule('bbcode','user', 'xmlcode', array('replace' => $content));
    } elseif ($attributes['default'] == 'csharp') {
        return xarTplModule('bbcode','user', 'csharpcode', array('replace' => $content));
    } elseif ($attributes['default'] == 'delphi') {
        return xarTplModule('bbcode','user', 'delphicode', array('replace' => $content));
    } elseif ($attributes['default'] == 'vb') {
        return xarTplModule('bbcode','user', 'vbcode', array('replace' => $content));
    } elseif ($attributes['default'] == 'python') {
        return xarTplModule('bbcode','user', 'pythoncode', array('replace' => $content));
    }
}
function do_bbcode_rtl ($action, $attributes, $content, $params, &$node_object)
{
    return xarTplModule('bbcode','user', 'rtl', array('replace' => $content));
}
?>