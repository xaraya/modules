<?php
/**
 * Utility function to retrieve the list of item types
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
 * Utility function to retrieve the list of item types of this module (if any)
 *
 * @author the MP3 Jukebox module development team
 * @return array containing the item types and their description
 */
function mp3jukebox_userapi_getitemtypes($args)
{
    $itemtypes = array();

   /*  do not use this if you only handle one type of items in your module */
   
       $itemtypes[1] = array('label' => xarVarPrepForDisplay(xarML('MP3Jukebox Songs')),
                          'title' => xarVarPrepForDisplay(xarML('View Songs')),
                          'url'   => xarModURL('mp3jukebox','user','viewsongs'));

       $itemtypes[2] = array('label' => xarVarPrepForDisplay(xarML('MP3Jukebox Playlists')),
                          'title' => xarVarPrepForDisplay(xarML('View Playlists')),
                          'url'   => xarModURL('mp3jukebox','user','view'));
   

    return $itemtypes;
}
?>
