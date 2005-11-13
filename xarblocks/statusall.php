<?php
/**
 * Example Block
 * 
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Sigmapersonnel Module
 * @author Michel V. 
 */

/**
 * initialise block
 */
function sigmapersonnel_statusall_init()
{
    return array(
        'numitems' => 5,
        'nocache' => 0, // cache by default (if block caching is enabled)
        'pageshared' => 1, // share across pages
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
}

/**
 * get information on block
 */
function sigmapersonnel_statusall_info()
{ 
    // Values
    return array(
        'text_type' => 'statusall',
        'module' => 'sigmapersonnel',
        'text_type_long' => xarML('Show status of total group)'),
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
} 

/**
 * display block
 */
function sigmapersonnel_statusall_display($blockinfo)
{ 
    // Security check
    if (!xarSecurityCheck('ReadSIGMAPersonnelBlock', 0, 'Block', $blockinfo['title'])) {return;}

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
    $items = xarModAPIFunc(
        'sigmapersonnel', 'user', 'getallpresence', // Get a function here to get all presences...
        array('numitems' => $vars['numitems'])
    );
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back
 
    // TODO: write the function(s) to calculate the amount of available people.
    // TODO: put that in an API function
 
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
            // $item['exid'],
            // array($item['name']));
            // Security check 2 - if the user has read access to the item, show a
            // link to display the details of the item
            if (xarSecurityCheck('ReadSIGMAPresence', 0, 'PresenceItem', "All:All:All]")) {
                $item['link'] = xarModURL(
                    'sigmapersonnel', 'user', 'display',
                    array('personid' => $item['personid'])
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
