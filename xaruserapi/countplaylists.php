<?php
/**
 * Utility function to count the number of playlists held by this module
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
 * Utility function to count the number of playlists held by this module
 *
 * @author the MP3 Jukebox module development team
 * @return integer number of playlists held by this module
 * @throws DATABASE_ERROR
 */
function mp3jukebox_userapi_countplaylists($args)
{
    extract($args);

    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn() we
     * currently just want the first playlist, which is the official database
     * handle. For xarDBGetTables() we want to keep the entire tables array
     * together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table and column definitions you are
     * getting - $table and $column don't cut it in more complex modules
     */
    $mp3jukebox_playlists_table = $xartable['mp3jukebox_playlists'];
    /* Get playlist - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read. Also, separating out the sql
     * statement from the Execute() command allows for simpler debug operation
     * if it is ever needed
     */
    $query = "SELECT COUNT(1)
            FROM $mp3jukebox_playlists_table";
    if(isset($uid) && is_int($uid)){
        $query .= " WHERE xar_uid = $uid ";
    }

    /* If there are no variables you can pass in an empty array for bind variables
     * or no parameter.
     */
    $result = &$dbconn->Execute($query,array());
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* Obtain the number of playlistss */
    list($numplaylists) = $result->fields;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the number of playlists */
    return $numplaylists;
}
?>
