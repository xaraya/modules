<?php
/**
 * File: $Id:
 * 
 * Utility function to pass individual item links 
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */
/**
 * utility function to pass individual item links to whoever
 * 
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 */
function courses_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewCourses', 0)) {
        return $itemlinks;
    }

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('courses', 'user', 'get',
            array('courseid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('courses', 'user', 'display',
                array('courseid' => $itemid)),
            'title' => xarML('Display Course'),
            'label' => xarVarPrepForDisplay($item['name']));
    }
    return $itemlinks;
}

?>
