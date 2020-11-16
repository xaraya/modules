<?php
/**
 * Uploads Module
 *
 * @package modules
 * @subpackage uploads module
 * @category Third Party Xaraya Module
 * @version 1.1.0
 * @copyright see the html/credits.html file in this Xaraya release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/index.php/release/eid/666
 * @author Uploads Module Development Team
 */

/**
 * Overview displays standard Overview page
 */
function uploads_admin_overview()
{
    $data=array();
    //just return to main function that displays the overview
    return xarTpl::module('uploads', 'admin', 'main', $data, 'main');
}
