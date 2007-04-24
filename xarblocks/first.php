<?php
/**
 * Example Block  - standard Initialization function
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */
/**
 * Example Block  - standard Initialization function
 *
 * @author Example Module development team
 * @return array
 */
function mp3jukebox_firstblock_init()
{
    return array(
        'numitems'    => 5,
        'nocache'     => 0, /* cache by default (if block caching is enabled) */
        'pageshared'  => 1, /* share across pages */
        'usershared'  => 1, /* share across group members */
        'cacheexpire' => null
    );
}

/**
 * Get information on block
 * @return array
 */
function mp3jukebox_firstblock_info()
{
    /* Values */
    return array(
        'text_type' => 'First',
        'module' => 'example',
        'text_type_long' => 'Show first example items (alphabetical)',
        'allow_multiple' => true,
        'form_content' => false,
        'form_refresh' => false,
        'show_preview' => true
    );
}

/**
 * Display block
 * @param array $blockinfo The array with all information this block needs
 * @return array $blockinfo
 */
function mp3jukebox_firstblock_display($blockinfo)
{
    /* Security check */
    if (!xarSecurityCheck('ReadExampleBlock', 0, 'Block', $blockinfo['name'])) {return;}

    /* Get variables from content block.
     * Content is a serialized array for legacy support, but will be
     * an array (not serialized) once all blocks have been converted.
     */
    if (!is_array($blockinfo['content'])) {
        $vars = @unserialize($blockinfo['content']);
    } else {
        $vars = $blockinfo['content'];
    }

    /* Defaults */
    if (empty($vars['numitems'])) {
        $vars['numitems'] = 5;
    }

    /* The API function is called. The arguments to the function are passed in
     * as their own arguments array.
     * Security check 1 - the getall() function only returns items for which the
     * the user has at least OVERVIEW access.
     */
    $items = xarModAPIFunc(
        'example', 'user', 'getall',
        array('numitems' => $vars['numitems'])
    );
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back

    /* TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
     * Loop through each item and display it.
     */
    $data['items'] = array();
    if (is_array($items)) {
        foreach ($items as $item) {
            /* Let any transformation hooks know that we want to transform some text
             * You'll need to specify the item id, and an array containing all the
             * pieces of text that you want to transform (e.g. for autolinks, wiki,
             * smilies, bbcode, ...).
             * Note : for your module, you might not want to call transformation
             * hooks in this overview list, but only in the display of the details
             * in the display() function.
             * list($item['name']) = xarModCallHooks('item',
             * 'transform',
             * $item['exid'],
             * array($item['name']));
             * Security check 2 - if the user has read access to the item, show a
             * link to display the details of the item
             */
            if (xarSecurityCheck('ReadExample', 0, 'Item', "$item[name]:All:$item[exid]")) {
                $item['link'] = xarModURL(
                    'example', 'user', 'display',
                    array('exid' => $item['exid'])
                );
                /* Security check 2 - else only display the item name (or whatever is
                 * appropriate for your module)
                 */
            } else {
                $item['link'] = '';
            }

            /* Add this item to the list of items to be displayed */
            $data['items'][] = $item;
        }
    }
    $data['blockid'] = $blockinfo['bid'];

    /* Now we need to send our output to the template.
     * Just return the template data.
     */
    $blockinfo['content'] = $data;

    return $blockinfo;
}
?>