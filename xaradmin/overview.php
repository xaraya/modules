<?php
/**
 * Overview for Wiki
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Wiki
 * @link http://xaraya.com/index.php/release/28.html
 */

/**
 * Overview displays standard Overview page
 */
function wiki_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTplModule('wiki', 'admin', 'main', $data, 'main');
}

?>