<?php
/**
 * File: $Id$
 *
 * Xaraya HTML Module
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage HTML Module
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
        xarExceptionSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM', new SystemException($msg));
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
    $transformtype = xarModGetVar('html', 'transformtype');
    if (!strlen(trim($text))) 
    {
        return '';
	}	
    if ($transformtype == 1){
        $text = preg_replace("/\n/si","<br />",$text);
    } elseif ($transformtype == 2){
//        $text = preg_replace("/\n/si","</p><p>",$text);
        $text = _clean_paragraphs($text);
    } elseif ($transformtype == 3){
        $text = $text;
    } elseif ($transformtype == 4){
        // If the string contains end of line type tags, assume the user
        // wants to provide html markup manually
        if( strpos($text,"<b") OR strpos($text,"<p") )
        {
            $text = $text;
        } else {
            // No html tags for dealing with end-of-line, transform as Breaks <br />
            $text = preg_replace("/\n/si","<br />",$text);
        }
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


function _clean_paragraphs($text) 
{
    static $clean = array();

    $hash = md5($text);
    if (isset($clean[$hash]) && $clean[$hash] == TRUE) {
        return $text;
    } else {
        $clean[$hash] = 1;
    }

    $value = $text;

    $value = preg_replace("/[\n\r]/", ' ', $value);

    // Change double p/br to double newline
    // Change single p/br to single newline
    // Change all multispace to one single space
    $value = preg_replace(array ('/(<[\/ ]*(p|br)[^>]*>[ \n\r]*){2,2}/',
                                 '/(<[\/ ]*(p|br)[^>]*>)/',
                                 '/[ ]+/'),
                          array("\n\n", "\n", ' '),
                          $value);

    $array = explode("\n", $value);
    $total = count($array) - 1;

    // If there is only one line, then encapsulate in it a p and return it.
    if ($total == 0) {
        return "\n<p>\n$array[0]\n</p>";
    }

    // otherwise, spool through the array and best guess the correct layout :)
    foreach ($array as $lineno => $line) {
        
        // Test for empty line
        $test = preg_replace('/\s*/', '', $line);
        
        if (!empty($test)) {
            
            // We handle the first and last lines differently
            if ($lineno == 0) {
                $line = "<p>\n$line";
            } elseif ($total == $lineno) {
                $new[] = "$line\n</p>";
                continue;
            }
            
            //  Here we want to maintain <br /> tags (which we've
            //  marked as single a single newline character
            if ($lineno != $total) {
                $next = trim($array[$lineno+1]);
                if (!empty($next)) {
                    $new[] = "$line<br />";
                } else {
                    $new[] = $line;
                }
            }
        } else { // This section is for dealing with empty lines
            
            // If this line is empty and it's the first, then add the opening
            // <p> tag - otherwise, if it's the last, add the closing </p> tag
            // -otherwise-, add a close followed by an open <p> tag
            
            if ($lineno == 0) {
                $new[] = "<p>";
            } elseif ($lineno == count($array) - 1) {
                $new[] = "</p>";
            } else {
                $new[] = "</p>\n<p>";
            }
        }
    }
    
    if (!isset($new) || empty($new)) {
        return '';
    } else {
        // Do some clean up
        $new = implode("\n", $new);
        $new = preg_replace("'<p>\n<\/p>'", '<p />', $new);
        do {
            // Shrink all double+ <p />'s down to one
            // Ie: if you have <p />\n<p />\n<p />\n<p /> --> turn it into: <p />\n
            if (stristr($new, "<p />\n<p />\n")) {
                $new = str_replace("<p />\n<p />\n", "<p />\n", $new);
            } else {
                break;
            }
        } while (stristr($new, "<p />\n<p />\n"));
    }
    return $new;

}


?>
