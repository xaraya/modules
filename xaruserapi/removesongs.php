<?php
/**
 * Get all mp3jukebox items
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
 * Get all mp3jukebox items
 *
 * @author the MP3 Jukebox module development team
 * @param int $args['numitems'] the number of items to retrieve (default -1 = all)
 * @param int $args['startnum'] start with this item number (default 1)
 * @return mixed array of items, or false on failure
 * @throws BAD_PARAM, DATABASE_ERROR, NO_PERMISSION
 */
function mp3jukebox_userapi_removesongs($args)
{
    /* Get arguments from argument array - all arguments to this function
     * should be obtained from the $args array, getting them from other places
     * such as the environment is not allowed, as that makes assumptions that
     * will not hold in future versions of Xaraya
     */
    extract($args);
    /* Optional arguments.
     * FIXME: (!isset($startnum)) was ignoring $startnum as it contained a null value
     * replaced it with ($startnum == "") (thanks for the talk through Jim S.) NukeGeek 9/3/02
     * if (!isset($startnum)) { */

    /* Argument check - make sure that all required arguments are present and
     * in the right format, if not then set an appropriate error message
     * and return
     * Note : since we have several arguments we want to check here, we'll
     * report all those that are invalid at the same time...
     */
    $invalid = array();
    if (!isset($playlistid) || !is_numeric($playlistid)) {
        $invalid[] = 'playlistid';
    }
    if (!isset($removesong) || !is_array($removesong)) {
        $invalid[] = 'removesong';
    }
    if (count($invalid) > 0) {
        $msg = xarML('Invalid #(1) for #(2) function #(3)() in module #(4)',
            join(', ', $invalid), 'user', 'removesongs', 'MP3Jukebox');
        xarErrorSet(XAR_SYSTEM_EXCEPTION, 'BAD_PARAM',
            new SystemException($msg));
        return;
    }

    /* Security check - important to do this as early on as possible to
     * avoid potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('EditMP3Jukebox')) return;
    /* Get database setup - note that both xarDBGetConn() and xarDBGetTables()
     * return arrays but we handle them differently. For xarDBGetConn() we
     * currently just want the first item, which is the official database
     * handle. For xarDBGetTables() we want to keep the entire tables array
     * together for easy reference later on
     */
    $dbconn =& xarDBGetConn();
    $xartable =& xarDBGetTables();
    /* It's good practice to name the table definitions you are
     * using - $table doesn't cut it in more complex modules
     */
    $mp3jukebox_playlist_songs_table = $xartable['mp3jukebox_playlist_songs'];

    /* Get items - the formatting here is not mandatory, but it does make the
     * SQL statement relatively easy to read. Also, separating out the sql
     * statement from the SelectLimit() command allows for simpler debug
     * operation if it is ever needed
     */
    $query = "DELETE FROM
                $mp3jukebox_playlist_songs_table
              WHERE
                xar_playlistid = ?
              AND
                xar_songid in (?)";
    /* We can also select on a part of the data. When the categories module is present,
     * you can use it to select a group of items based on the category they are in.
     * Then replace the query above with the query below, and make sure you pass the catid in your templates.
     */

    /* SelectLimit also supports bind variable, they get to be put in
     * as the last parameter in the function below. In this case we have no
     * bind variables, so we left the parameter out. We could have passed in an
     * empty array though.
     */
    $result = $dbconn->Execute($query, array($playlistid, implode(',', $removesong)));
    /* Check for an error with the database code, adodb has already raised
     * the exception so we just return
     */
    if (!$result) return;
    /* All successful database queries produce a result set, and that result
     * set should be closed when it has been finished with
     */
    $result->Close();
    /* Return the items */
    return true;
}
?>
