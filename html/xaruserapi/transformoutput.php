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
    $p = "<p>";
    $text = str_replace(chr(13), "</p>$p" , $string );
    $text = $p. $string. "</p>\n";
}

?>
