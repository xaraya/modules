<?php
/**
 * Xaraya HighLight
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage highlight
 * @link http://xaraya.com/index.php/release/559.html
 * @author Curtis Farnham <curtis@farnham.com>
 */
/**
 * Add a standard screen upon entry to the module.
 *
 * @return array output for the template with examples
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