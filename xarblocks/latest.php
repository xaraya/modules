<?php
/**
 * Release Block
 * 
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Release Module
 * @link http://xaraya.com/index.php/release/773.html
 */

/**
 * initialise block
 * @return array
 */
function release_latestblock_init()
{
    return array(
        'numitems' => 5,
        'nocache' => 0, // cache by default
        'pageshared' => 1,
        'usershared' => 1, // share across group members
        'cacheexpire' => null
    );
} 

/**
 * get information on block
 * @return array
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
 * @return array
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
    if (!isset($vars['shownonfeeditems']) || empty($vars['shownonfeeditems'])) {
        $vars['shownonfeeditems'] = 0;
    }
    $usefeed = ($vars['shownonfeeditems'] == 0)?1:null; //null - no selection on usefeed, 1 selecct for rss only
    // The API function is called to get all notes
    $items = xarModAPIFunc('release', 'user', 'getallnotes',
                     array('numitems' => $vars['numitems'],
                           'usefeed'  => $usefeed)
                          );
    
    if (!isset($items) && xarCurrentErrorType() != XAR_NO_EXCEPTION) {return;} // throw back

    // TODO: check for conflicts between transformation hook output and xarVarPrepForDisplay
    // Loop through each item and display it.
    $data['items'] = array();
    if (is_array($items)) {
        foreach ($items as $item) {
            // Security check 2 - if the user has read access to the item, show a
            // link to display the details of the item
            if (xarSecurityCheck('OverviewRelease', 0, 'Item', "$item[rnid]:All:$item[eid]")) {
                $item['link'] = xarModURL(
                    'release', 'user', 'displaynote',
                    array('rnid' => $item['rnid'])
                );

                // Security check 2 - else only display the item name (or whatever is
                // appropriate for your module)
            } else {
                $item['link'] = '';
            }
            $roles = new xarRoles();
            $role = $roles->getRole($item['uid']);
            $item['author']= $role->getName();
            $item['authorlink']=xarModURL('roles','user','display',array('uid'=>$item['uid']));            
            // Add this item to the list of items to be displayed
            $data['items'][] = $item;

        }
    }
    $exttypes = xarModAPIFunc('release','user','getexttypes');
    $data['exttypes']=$exttypes;
    $data['blockid'] = $blockinfo['bid'];
    // Now we need to send our output to the template.
    // Just return the template data.
    $blockinfo['content'] = $data;

    return $blockinfo;
}

?>