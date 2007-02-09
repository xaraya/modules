<?php
/**
* view available languages
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
* view available languages
*/
function highlight_admin_languages()
{
    // security check
    if (!xarSecurityCheck('AdminHighlight')) return;

    // set defaults
    $cols = 2; // make this dynamic?

    // get vars
    $languages = xarModAPIFunc('highlight', 'user', 'getlanguages');
    $rowpercol = ceil(count($languages)/$cols);

    // initialize template vars
    $data = xarModAPIFunc('highlight', 'admin', 'menu');

    // set template vars
    $data['languages'] = $languages;
    $data['rowpercol'] = $rowpercol;

    return $data;
}

?>
