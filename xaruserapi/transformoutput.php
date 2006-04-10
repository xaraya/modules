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
 */
function html_userapitransformoutput($text)
{
    /* TODO: are there any extension plans here?, otherwise this code can be replaced with something which
     * does NOT depend on the bbcode module, nor the (big) class. From casual inspection it looks as if this does
     * only nl2br on the $text?
     */
   /* include_once 'modules/bbcode/xarclass/stringparser_bbcode.class.php';
    $bbcode = new StringParser_BBCode();
    $dotransform = xarModGetVar('html', 'dolinebreak');
    if ($dotransform == 1){
        $bbcode->addParser(array ('block', 'inline', 'link', 'listitem'), 'nl2br');
        $bbcode->setRootParagraphHandling(true);
    }
    $text = $bbcode->parse($text);
    */

    $dotransform = xarModGetVar('html', 'dolinebreak');
    if (!strlen(trim($text))) {
        return '';
    }
    if ($dotransform == 1){
        $text = preg_replace("/\n/si","<br />",$text);
    } else {
        $text = $text;
    }
    //$text = preg_replace("/(\015\012)|(\015)|(\012)/","</p><p>",$text); 
    // This call is what is driving the bugs because it is transforming more
    // than we want.  The problem without the call though, it the output from
    // this function is not xhtml compliant.
    //
    // So, a configuration in the html script will allow a replacement of
    // paragraphs or line breaks.  If paragraphs are used, the template must
    // open and close the paragraphs tags before and after the transformed output.
    //$text = "<p> " . $text . " </p>\n";
   $text = str_replace ("<p></p>", "", $text);
    return $text;
}
?>