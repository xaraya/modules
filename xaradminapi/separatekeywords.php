<?php
/**
 * Keywords Module
 *
 * @package modules
 * @subpackage keywords module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/187.html
 * @author janez(Alberto Cazzaniga)
 */
/**
 * Now using 'strlist' validation to do the hard work.
 * @return array
 */
function keywords_adminapi_separatekeywords($args)
{
    extract($args);

    $delimiters = xarModVars::get('keywords', 'delimiters');

    // Colons are the only character we can't use (ATM).
    // TODO: remove this then xarVar::validate() is able to handle escape
    // sequences for colons as data in the validation rules.
    str_replace(':', '', $delimiters);

    // Ensure we can fall back to a default.
    if (empty($delimiters)) {
        $delimiters = ';';
    }

    // Get first delimiter for creating the array.
    $first = substr($delimiters, 0, 1);

    // Normalise the delimiters and trim the strings.
    xarVar::validate("strlist:$delimiters:pre:trim", $keywords);

    // Explode into an array of words.
    $words = explode($first, $keywords);

    return $words;
}
