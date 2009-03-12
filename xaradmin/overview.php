<?php
/**
 * Displays standard Overview page
 *
 * @package modules
 * @copyright (C) 2002-2009 The Digital Development Foundation
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
 * @author the Example module development team
 * @return array
 */
function example_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminExample',0)) return;

    /* The overview function doesn't need any data so we can pass an empty array.
     * 
     * Formerly these overviews have been in the admin-main.xd templates which is
     * outdated since March 2009. On localized sites the file
     * var/locales/xx_XX.utf-8/xml/modules/example/templates/admin-main.xml should
     * be renamed to admin-overview.xml to preserve the translation.
     */

    return array();
}

?>
