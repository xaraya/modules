<?php
/**
 * Keywords Module
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author janez(Alberto Cazzaniga)
*/
/**
 * Now using 'strlist' validation to do the hard work.
 * @return array
 */
function keywords_adminapi_separekeywords($args)
{
    extract($args);

    if (!xarSecurityCheck('AdminKeywords')) return;

    $delimiters = xarModGetVar('keywords', 'delimiters');

    // Colons are the only character we can't use (ATM).
    // TODO: remove this then xarVarValidate() is able to handle escape
    // sequences for colons as data in the validation rules.
    str_replace(':', '', $delimiters);

    // Ensure we can fall back to a default.
    if (empty($delimiters)) {
        // Provide a default.
        $delimiters = ';';
    }

    // Get first delimiter for creating the array.
    $first = substr($delimiters, 0, 1);

    // Normalise the delimiters and trim the strings.
    xarVarValidate("strlist:$delimiters:pre:trim", $keywords);

    // Explode into an array of words.
    $words = explode($first, $keywords);

    return $words;
}
?>