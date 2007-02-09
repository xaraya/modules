<?php
/**
* Display module overview
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
* Display module overview
*/
function highlight_admin_overview()
{
    // security check
    if (!xarSecurityCheck('AdminHighlight', 0)) return;

    // get vars
    $string = xarModGetVar('highlight', 'string');
    $samplecode = xarModAPIFunc('highlight', 'user', 'getsamplecode', array('string' => $string));
    $htmlsample = htmlspecialchars($samplecode);
    $transformed = xarModAPIFunc('highlight', 'user', 'transform',
        array('extrainfo' => $samplecode)
    );

    // initialize template data
    $data = xarModAPIFunc('highlight', 'admin', 'menu');

    // generate template vars
    $data['string'] = $string;
    $data['samplecode'] = $samplecode;
    $data['htmlsample'] = $htmlsample;
    $data['transformsample'] = $transformed;

    // show overview page
    return xarTplModule('highlight', 'admin', 'main', $data, 'main');
}

?>