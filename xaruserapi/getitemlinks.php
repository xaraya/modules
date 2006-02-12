<?php
/**
 * Utility function to pass individual item links
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses Module
 * @link http://xaraya.com/index.php/release/179.html
 * @author Courses module development team
 */
/**
 * utility function to pass individual item links to whoever
 *
 * @param  int $args ['itemtype'] item type (optional)
 * @param  array $args ['itemids'] array of item ids to get
 * @returns array
 * @return array containing the itemlink(s) for the item(s).
 * @todo implement this function for planned courses and courses themselves
 */
function courses_userapi_getitemlinks($args)
{
    extract ($args);
    // Create the array
    $itemlinks = array();
    if (!xarSecurityCheck('ViewCourses', 0)) {
        return $itemlinks;
    }
    if (isset($itemtype) && $itemtype <1000) {
        foreach ($args['itemids'] as $itemid) {
            $item = xarModAPIFunc('courses', 'user', 'get',
                array('courseid' => $itemid));
            if (!isset($item)) return;
            $itemlinks[$itemid] = array('url' => xarModURL('courses', 'user', 'display',
                                                    array('courseid' => $itemid)),
                                        'title' => xarML('Display Course'),
                                        'label' => xarVarPrepForDisplay($item['name']));
        }
    } else {
            if (!isset($item)) return;
            $itemlinks[$itemid] = array('url' => xarModURL('courses', 'admin', 'display',
                                                    array('itemid'   => $itemid,
                                                          'itemtype' => $itemtype)),
                                        'title' => xarML('Display Course Parameter'),
                                        'label' => xarML('Course parameter')
                                       );
    return $itemlinks;
}
?>
