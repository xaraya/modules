<?php
/**
 * Others block initialisation
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses
 * @author Courses module development team
 */

/**
 * initialise block
 */
function courses_othersblock_init()
{
    return array(
        'numitems' => 5
    );
}

/**
 * get information on block
 */
function courses_othersblock_info()
{
    // Values
    return array(
        'text_type' => 'Others',
        'module' => 'courses',
        'text_type_long' => 'Show other course items when 1 is displayed',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 */
function courses_othersblock_display($blockinfo)
{
    // See if we are currently displaying an example item
    // (this variable is set in the user display function)
    if (!xarVarIsCached('Blocks.courses', 'courseid')) {
        // if not, we don't show this
        return;
    }
    // Optional arguments.
    if (!isset($startnum)) {
        $startnum = 1;
    }

    $current_courseid = xarVarGetCached('Blocks.courses', 'courseid');
    if (empty($current_courseid) || !is_numeric($current_courseid)) {
        return;
    }
    // Security check
    if (!xarSecurityCheck('ReadCoursesBlock', 1, 'Block', $blockinfo['title'])) return;

    // Get variables from content block.
    // Content is a serialized array for legacy support, but will be
    // an array (not serialized) once all blocks have been converted.
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // Database information
    xarModDBInfoLoad('courses');
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    $coursestable = $xartable['courses'];

    // Query
    $sql = "SELECT xar_courseid, xar_name
            FROM $coursestable
            WHERE xar_courseid != ?
            ORDER by xar_courseid DESC";
    $result = $dbconn->SelectLimit($sql, $vars['numitems'] , $startnum-1, array($current_courseid));

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }

    // Create output object
    $items = array();

    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name) = $result->fields;

        if (xarSecurityCheck('ViewCourses', 0, 'Item', "$name:All:$courseid")) {
            if (xarSecurityCheck('ReadCourses', 0, 'Item', "$name:All:$courseid")) {
                $item = array();
                $item['link'] = xarModURL(
                    'courses', 'user', 'display',
                    array('courseid' => $courseid)
                );

            }
            $item['name'] = $name;
        }
        $items[] = $item;
    }

    $blockinfo['content'] = array('items' => $items);

    return $blockinfo;
}

?>