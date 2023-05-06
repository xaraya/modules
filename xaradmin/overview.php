<?php
/**
 * Hitcount Module
 *
 * @package modules
 * @subpackage hitcount module
 * @category Third Party Xaraya Module
 * @version 2.0.0
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/177.html
 * @author Hitcount Module Development Team
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author the Hitcount module development team
 * @return array xarTpl::module with $data containing template data
 * @since 4 March 2006
 */
function hitcount_admin_overview()
{
    /* Security Check */
    if(!xarSecurity::check('AdminHitcount',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTpl::module('hitcount', 'admin', 'main', $data,'main');
}

?>
