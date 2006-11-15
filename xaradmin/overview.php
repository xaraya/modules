<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Smilies
 * @link http://xaraya.com/index.php/release/153.html
 */
/**
 * Overview function that displays the standard Overview page
 *
 */
function smilies_admin_overview()
{
    // Security Check
    if (!xarSecurityCheck('AdminSmilies',0)) return;

    $data = array();

    // Get the current smilies for an overview.
    $smilies = xarModAPIFunc('smilies', 'user', 'getall');

    // Sort by icon
    foreach($smilies as $smilie) {
        $data['icons'][$smilie['icon']][] = $smilie;
    }

    // if there is a separate overview function return data to it
    // else just call the main function that displays the overview

    return xarTplModule('smilies', 'admin', 'main', $data, 'main');
}

?>
