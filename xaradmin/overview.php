<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author the Example module development team
 * @return array xarTplModule with $data containing template data
 * @since 3 Sept 2005
 */
function twitter_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminExample',0)) return;

    $data=array();

    return $data;
}

?>
