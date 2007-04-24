<?php
/**
 * Get a specific item
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
 * Get a specific item
 *
 * Standard function of a module to retrieve a specific item
 *
 * @author the MP3 Jukebox module development team
 * @param  int $args ['exid'] id of mp3jukebox item to get
 * @return mixed  item array, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function mp3jukebox_userapi_getsong($args)
{
    /* Get arguments from argument array - all arguments to this function
     * should be obtained from the $args array, getting them from other places
     * such as the environment is not allowed, as that makes assumptions that
     * will not hold in future versions of Xaraya
     */
    extract($args);
    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     */
    if (!isset($songid) || !is_numeric($songid)) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            'song ID', 'user', 'getsong', 'MP3Jukebox');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn() we
     * currently just want the first item, which is the official database
     * handle. For xarDBGetTables() we want to keep the entire tables array
     * together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $mp3jukebox_songs_table = $xartable['mp3jukebox_songs'];
    /* Get item - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read. Also, separating out the sql
     * statement from the Execute() command allows for simpler debug operation
     * if it is ever needed
     */
    $query = "SELECT xar_songid,
                    xar_song_filesize,
                    xar_createdate,
                    xar_status,
                    xar_song_name,
                    xar_song_filename,
                    xar_artist_name,
                    xar_album_name,
                    xar_song_filetype
              FROM $mp3jukebox_songs_table
              WHERE xar_songid = ?";
    $result = &$dbconn->Execute($query,array($songid));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Check for no rows found, and if so, close the result set and return an exception */
    if ($result->EOF) {
        $result->Close();
        $msg = xarML('This item does not exist');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'ID_NOT_EXIST',
            new SystemException(__FILE__ . '(' . __LINE__ . '): ' . $msg));
        return;
    }
    /* Obtain the item information from the result set */
    list($songid, $song_filesize, $createdate, $status, $song_name, $song_filename, $artist_name, $album_name, $song_filetype) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Security check - important to do this as early on as possible to avoid
     * potential security holes or just too much wasted processing. Although
     * this one is a bit late in the function it is as early as we can do it as
     * this is the first time we have the relevant information.
     * For this function, the user must *at least* have READ access to this item
     */
    if (!xarSecurityCheck('ReadMP3Jukebox', 1, 'Song', "$song_name:All:$songid")) {
        return;
    }
    /* Create the item array */
    $item = array('songid' => $songid,
                  'song_filesize' => $song_filesize,
                  'createdate' => $createdate,
                  'status' => $status,
                  'song_name' => $song_name,
                  'song_filename' => $song_filename,
                  'artist_name' => $artist_name,
                  'album_name' => $album_name,
                  'song_filetype' => $song_filetype
            );
    /* Return the item array */
    return $item;
}
?>
