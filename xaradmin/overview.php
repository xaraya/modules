<?php
/**
 * Courses show overview page with help texts
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author MichelV <michelv@xarayahosting.nl>
 * @return array xarTplModule with $data containing template data
 * @since 4 March 2006
 */
function courses_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminCourses',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('courses', 'admin', 'main', $data,'main');
}

?>
