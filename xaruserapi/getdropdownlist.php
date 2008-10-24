<?php
/**
 * Get an id - name listing suitable for dropdown
 *
 * @package modules
 * @copyright (C) 2002-2008 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * get a course dropdown listing
 *
 * @author the Courses module development team
 * @return array with item, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function courses_userapi_getdropdownlist($args)
{
    extract($args);

    if (isset($level) && $level <> -1) {
       $option['level'] = $level;
    }
    if (isset($orderlist) && !empty($orderlist)) {
        $options['order'] = implode(',', $orderlist);
    }
    if (isset($cids) && !empty($cids)) {
        $options['cids'] = $cids;
    }
    if (isset($coursetype) && ($coursetype <> -1)) {
        $options['coursetype'] = $coursetype;
    }
    $options = array();
    $courses = xarModAPIFunc('courses', 'user', 'getall', $options);
    if (is_array($courses)) {
        foreach ($courses as $course) {
            $coursename = xarVarPrepForDisplay($course['number']).' '.xarVarPrepForDisplay($course['name']);
            $options[ $course['courseid']] = $coursename;
        }
    }
    return $options;    
}
?>
