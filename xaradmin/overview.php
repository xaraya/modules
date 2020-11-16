<?php
/**
 * HTML Module
 *
 * @package modules
 * @subpackage html module
 * @category Third Party Xaraya Module
 * @version 1.5.0
 * @copyright see the html/credits.html file in this release
 * @link http://www.xaraya.com/index.php/release/779.html
 * @author John Cox
 */

/**
 * Overview displays standard Overview page
 *
 * Only used if you actually supply an overview link in your adminapi menulink function
 * and used to call the template that provides display of the overview
 *
 * @returns array xarTpl::module with $data containing template data
 * @return array containing the menulinks for the overview item on the main manu
 * @since 2 Oct 2005
 */
function html_admin_overview()
{
    /* Security Check */
    if (!xarSecurity::check('AdminHTML')) {
        return;
    }

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that usually displays the overview
     */

    return xarTpl::module('html', 'admin', 'main', $data, 'main');
}
