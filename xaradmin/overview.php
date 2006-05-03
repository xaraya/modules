<?php
/**
 * Standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Encyclopedia Module
 * @link http://xaraya.com/index.php/release/221.html
 * @author Marc Lutolf
 */
/**
 * Overview function that displays the standard Overview page
 */
function encyclopedia_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminEncyclopedia',0)) return;

    $data=array();

    return xarTplModule('encyclopedia', 'admin', 'main', $data,'main');
}

?>
