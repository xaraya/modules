<?php
/**
 * File: $Id$
 * 
 * Release Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2003 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage release
 * @author Release module development team 
 */

/**
 * initialise block
 */
function release_latestblock_init()
{
    return array(
        'numitems' => 5
    );
} 

/**
 * get information on block
 */
function release_latestblock_info()
{ 
    // Values
    return array('text_type' => 'Latest',
        'module' => 'release',
        'text_type_long' => 'Show latest release notes',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true);
} 

/**
 * display block
 */
function release_latestblock_display($blockinfo)
{ 
    // Security check
    if (!xarSecurityCheck('ReadReleaseBlock', 1, 'Block', $blockinfo['title'])) {return;}

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

    // The API function is called.  The arguments to the function are passed in
    // as their own arguments array.
    // Security check 1 - the getall() function only returns items for which the
    // the user has at least OVERVIEW access.
    // Item must also be approved
    $items = xarModAPIFunc(
        'release', 'user', 'getallnotes',
        array('numitems' => $vars['numitems'],
              'approved' => 2)
    );
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back
 
    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    $data['items'] = array();
    if (is_array($items)) {
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
            // $item['rid'],
            // array($item['name']));
            // Security check 2 - if the user has read access to the item, show a
            // link to display the details of the item
            if (xarSecurityCheck('OverviewRelease', 0, 'Item', "$item[rnid]:All:$item[rid]")) {
                $item['link'] = xarModURL(
                    'release', 'user', 'displaynote',
                    array('rnid' => $item['rnid'])
                ); 
                // Security check 2 - else only display the item name (or whatever is
                // appropriate for your module)
            } else {
                $item['link'] = '';
            } 

            // Add this item to the list of items to be displayed
            $data['items'][] = $item;
        }
    }
    $data['blockid'] = $blockinfo['bid'];

    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
} 

?>
