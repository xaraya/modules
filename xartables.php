<?php
/**
 * Table definition functions
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php
 * @author MP3 Jukebox Module Development Team
 */
/**
 * Table definition functions
 *
 * Return MP3 Jukebox module table names to xaraya
 *
 * This function is called internally by the core whenever the module is
 * loaded. It is loaded by xarMod__loadDbInfo().
 * @author MP3 Jukebox Module development team
 * @access private
 * @return array
 */
function mp3jukebox_xartables()
{
    /* Initialise table array */
    $xarTables = array();
    /* Get the name for the mp3jukebox item table. This is not necessary
     * but helps in the following statements and keeps them readable
     */
    $mp3jukebox_songs_table = xarDBGetSiteTablePrefix() . '_mp3jukebox_songs';
	$mp3jukebox_playlists_table = xarDBGetSiteTablePrefix() . '_mp3jukebox_playlists';
	$mp3jukebox_playlist_songs_table = xarDBGetSiteTablePrefix() . '_mp3jukebox_playlist_songs';

    /* Set the table name */
    $xarTables['mp3jukebox_songs'] = $mp3jukebox_songs_table;
    $xarTables['mp3jukebox_playlists'] = $mp3jukebox_playlists_table;
    $xarTables['mp3jukebox_playlist_songs'] = $mp3jukebox_playlist_songs_table;

    /* Return the table information */
    return $xarTables;
}
?>