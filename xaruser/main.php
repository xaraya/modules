<?php
/* * Xaraya Smilies
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL <http://www.gnu.org/licenses/gpl.html>
 * @link http://www.xaraya.org
 *
 * @subpackage Smilies Module
 * @author Jim McDonald, Mikespub, John Cox
*/

/**
 * Add a standard screen upon entry to the module.
 * @returns output
 * @return output with smilies Menu information
 */
function highlight_user_main()
{
    // set defaults
    $cols = 2; // make this dynamic?

    // get vars
    $string = xarModGetVar('highlight', 'string');
    $samplecode = xarModAPIFunc('highlight', 'user', 'getsamplecode');
    $htmlsample = htmlspecialchars($samplecode);
    $transformed = xarModAPIFunc('highlight', 'user', 'transform',
        array('extrainfo' => $samplecode)
    );
    $languages = xarModAPIFunc('highlight', 'user', 'getlanguages');
    $rowpercol = ceil(count($languages)/$cols);

    // initialize template data
    $data = xarModAPIFunc('highlight', 'admin', 'menu');

    // generate template vars
    $data['string'] = $string;
    $data['samplecode'] = $samplecode;
    $data['htmlsample'] = $htmlsample;
    $data['transformsample'] = $transformed;
    $data['languages'] = $languages;
    $data['rowpercol'] = $rowpercol;

    return $data;
}
?>