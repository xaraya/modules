<?php
/**
 * File: $Id: s.others.php 1.8 03/03/18 02:35:04-05:00 johnny@falling.local.lan $
 * 
 * Example block initialisation
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
 * initialise block
 */
function courses_othersblock_init()
{
    return true;
}

/**
 * get information on block
 */
function courses_othersblock_info()
{
    // Values
    return array('text_type' => 'Others',
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

    $current_courseid = xarVarGetCached('Blocks.courses', 'courseid');
    if (empty($current_courseid) || !is_numeric($current_courseid)) {
        return;
    }
    // Security check
    if (!xarSecurityCheck('ReadCoursesBlock', 1, 'Block', $blockinfo['title'])) return;
    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);
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
    $sql = "SELECT xar_courseid,
                   xar_name
            FROM $coursestable
            WHERE xar_courseid != '" . xarVarPrepForStore($current_courseid) . "'
            ORDER by xar_courseid DESC";
    $result = $dbconn->SelectLimit($sql, $vars['numitems']);

    if ($dbconn->ErrorNo() != 0) {
        return;
    }

    if ($result->EOF) {
        return;
    }
    // Create output object
    $output = new pnHTML();
    // Display each item, permissions permitting
    for (; !$result->EOF; $result->MoveNext()) {
        list($courseid, $name) = $result->fields;

        if (xarSecurityCheck('ViewCourses', 0, 'Item', "$name:All:$courseid")) {
            if (xarSecurityCheck('ReadCourses', 0, 'Item', "$name:All:$courseid")) {
                $output->URL(xarModURL('courses',
                        'user',
                        'display',
                        array('courseid' => $courseid)),
                    $name);
            } else {
                $output->Text($name);
            }
            $output->Linebreak();
        }
    }
    $output->Linebreak();
    // TODO: shouldn't this stuff be BL-able too ??
    // Besides the fact that title & content are placed according to some
    // master block template, why can't we create content via BL ?
    // Populate block info and pass to theme
    $blockinfo['content'] = $output->GetOutput();
    return $blockinfo;
}

/**
 * modify block settings
 */
function courses_othersblock_modify($blockinfo)
{
    // Get current content
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // Send content to template
    $output = xarTplBlock('courses', 'othersAdmin', array('numitems' => $vars['numitems'], 'blockid' => $blockinfo['bid']));
    // Return output
    return $output;
}

/**
 * update block settings
 */
function courses_othersblock_update($blockinfo)
{
    if (!xarVarFetch('numitems', 'isset', $vars['numitems'], NULL, XARVAR_DONT_SET)) return;

    // Define a default block title
    if (empty($blockinfo['title'])) {
        $blockinfo['title'] = xarML('Other course items');
    }

    $blockinfo['content'] = serialize($vars);

    return $blockinfo;
}

?>
