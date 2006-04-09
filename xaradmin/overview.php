<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Keywords Module
 * @link http://xaraya.com/index.php/release/187.html
 * @author mikespub
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 */
function keywords_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminKeywords',0)) return;

    $data=array();

    return xarTplModule('keywords', 'admin', 'main', $data,'main');
}

?>
