<?php
/**
 * Utility function to pass individual item links to whoever
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author MP3 Jukebox Module Development Team
 */
 /**
 * Utility function to pass individual item links to whoever
 * 
 * @author the MP3 Jukebox module development team
 * @param  $args ['itemtype'] item type (optional)
 * @param  $args ['itemids'] array of item ids to get
 * @return array containing the itemlink(s) for the item(s).
 */
function mp3jukebox_userapi_getitemlinks($args)
{
    $itemlinks = array();
    if (!xarSecurityCheck('ViewMP3Jukebox', 0)) {
        return $itemlinks;
    } 

    foreach ($args['itemids'] as $itemid) {
        $item = xarModAPIFunc('mp3jukebox', 'user', 'get',
            array('playlistid' => $itemid));
        if (!isset($item)) return;
        $itemlinks[$itemid] = array('url' => xarModURL('mp3jukebox', 'user', 'display',
                array('playlistid' => $itemid)),
            'title' => xarML('Display Playlist'),
            'label' => xarVarPrepForDisplay($item['title']));
    } 
    return $itemlinks;
} 
?>
