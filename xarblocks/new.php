<?php
/**
 * Timeplanning Block
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Courses
 * @author Courses module development team
 */

/**
 * initialise block
 * @todo MichelV: Make this block work
 */
function courses_newblock_init()
{
    return true;
}

/**
 * get information on block
 */
function courses_newblock_info()
{
    // Values
    return array('text_type' => 'New',
        'module' => 'courses',
        'text_type_long' => 'Show new courses (alphabetical)',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
}

/**
 * display block
 */
function courses_newblock_display($blockinfo)
{
    // Security check
    if (!xarSecurityCheck('ReadCoursesBlock', 1, 'Block', $blockinfo['title'])) return;
    // Get variables from content block
    $vars = @unserialize($blockinfo['content']);
    // Defaults
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }
    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    $items = xarModAPIFunc('courses',
        'user',
        'getall',
        array('numitems' => $vars['numitems']));
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    foreach ($items as $item) {
        // Let any transformation hooks know that we want to transform some text
        // You'll need to specify the item id, and an array containing all the
        // pieces of text that you want to transform (e.g. for autolinks, wiki,
        // smilies, bbcode, ...).
        // Note : for your module, you might not want to call transformation
        // hooks in this overview list, but only in the display of the details
        // in the display() function.
        // list($item['name']) = xarModCallHooks('item',
        // 'transform',
        // $item['exid'],
        // array($item['name']));
        // Security check 2 - if the user has read access to the item, show a
        // link to display the details of the item
        if (xarSecurityCheck('ReadCourses', 0, 'Item', "$item[name]:All:$item[courseid]")) {
            $item['link'] = xarModURL('courses',
                'user',
                'display',
                array('courseid' => $item['courseid']));
            // Security check 2 - else only display the item name (or whatever is
            // appropriate for your module)
        } else {
            $item['link'] = '';
        }
        // Clean up the item text before display
        $item['name'] = xarVarPrepForDisplay($item['name']);
        // Add this item to the list of items to be displayed
        $data['items'][] = $item;
    }
    $data['blockid'] = $blockinfo['bid'];

    // Lets find out the template that we are sending the data to.
    if (empty($blockinfo['template'])) {
        $template = 'new';
    } else {
        $template = $blockinfo['template'];
    }
    // Now we need to send our output to the template.
    $blockinfo['content'] = xarTplBlock('courses', $template, $data);

    return $blockinfo;
}

?>