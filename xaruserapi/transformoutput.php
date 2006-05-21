<?php
/**
 * Xaraya HTML Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage HTML Module
 * @link http://xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Transform text
 *
 * @public
 * @author John Cox 
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 * @raise BAD_PARAM
 */
function html_userapi_transformoutput($args)
{
    // Get arguments from argument array
    extract($args);

    // Argument check
    if (!isset($extrainfo)) {
        $msg = xarML('Invalid Parameter #(1) for #(2) function #(3)() in module #(4)',
                     'extrainfo', 'userapi', 'transformoutput', 'html');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
        return;
    }

    if (is_array($extrainfo)) {
        if (isset($extrainfo['transform']) && is_array($extrainfo['transform'])) {
            foreach ($extrainfo['transform'] as $key) {
                if (isset($extrainfo[$key])) {
                    $extrainfo[$key] = html_userapitransformoutput($extrainfo[$key]);
                }
            }
            return $extrainfo;
        }
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = html_userapitransformoutput($text);
        }
    } else {
        $transformed = html_userapitransformoutput($text);
    }

    return $transformed;
}

/**
 * Transform text api
 *
 * @private
 * @author John Cox
 * @author Matthew Mullenweg - credit for smart linebreak transforms
 */

function html_userapitransformoutput($text)
{
   /* include_once 'modules/bbcode/xarclass/stringparser_bbcode.class.php';
    $bbcode = new StringParser_BBCode();
    $dotransform = xarModGetVar('html', 'dolinebreak');
    if ($dotransform == 1){
        $bbcode->addParser(array ('block', 'inline', 'link', 'listitem'), 'nl2br');
        $bbcode->setRootParagraphHandling(true);
    }
    $text = $bbcode->parse($text);
    */

    if (strlen(trim($text)) == 0) return '';

    $dotransform = xarModGetVar('html', 'dolinebreak');
    $br=0;
    if ($dotransform == 1){
  	    $text = $text . "\n"; // just to make things a little easier, pad the end
	    $text = preg_replace('|<br />\s*<br />|', "\n\n", $text);
	    $text = preg_replace('!(<(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)!', "\n$1", $text);
	    $text = preg_replace('!(</(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])>)!', "$1\n\n", $text);
	    $text = str_replace(array("\r\n", "\r"), "\n", $text); // cross-platform newlines
	    $text = preg_replace("/\n\n+/", "\n\n", $text); // take care of duplicates
	    $text = preg_replace('/\n?(.+?)(?:\n\s*\n|\z)/s', "<p>$1</p>\n", $text); // make paragraphs, including one at the end
	    $text = preg_replace('|<p>\s*?</p>|', '', $text); // under certain strange conditions it could create a P of entirely whitespace
	    $text = preg_replace('!<p>\s*(</?(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|hr|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)\s*</p>!', "$1", $text);
	    $text = preg_replace("|<p>(<li.+?)</p>|", "$1", $text); // problem with nested lists
	    $text = preg_replace('|<p><blockquote([^>]*)>|i', "<blockquote$1><p>", $text);
	    $text = str_replace('</blockquote></p>', '</p></blockquote>', $text);
	    $text = preg_replace('!<p>\s*(</?(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|hr|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)!', "$1", $text);
	    $text = preg_replace('!(</?(?:table|thead|tfoot|caption|colgroup|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)\s*</p>!', "$1", $text);
	    if ($br) $text = preg_replace('|(?<!<br />)\s*\n|', "<br />\n", $text); // optionally make line breaks
        $text = preg_replace('!(</?(?:table|thead|tfoot|caption|tbody|tr|td|th|div|dl|dd|dt|ul|ol|li|pre|select|form|blockquote|address|math|p|h[1-6])[^>]*>)\s*<br />!', "$1", $text);
	    $text = preg_replace('!<br />(\s*</?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)!', '$1', $text);
	    $text = preg_replace('!(<pre.*?>)(.*?)</pre>!ise', " stripslashes('$1') .  stripslashes(clean_pre('$2'))  . '</pre>' ", $text);
    } else {
        $text = $text;
    }

    return $text;
}
function clean_pre($text) {
	$text = str_replace('<br />', '', $text);
	$text = str_replace('<p>', "\n", $text);
	$text = str_replace('</p>', '', $text);
	return $text;
}
?>