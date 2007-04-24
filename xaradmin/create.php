<?php
/**
 * Create a new Song
 *
 * @package modules
 * @copyright (C) 2002-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author MP3 Jukebox Module Development Team
 */
/**
 * Create a new Song
 *
 * Standard function to create a new song
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('mp3jukebox','admin','new') to create a new song
 *
 * @author MP3 Jukebox module development team
 * @param  string $args['name']   the name of the item to be created
 * @param  int    $args['number'] the number of the item to be created
 */
function mp3jukebox_admin_create($args)
{
    /* Admin functions of this type can be called by other modules. If this
     * happens then the calling module will be able to pass in arguments to
     * this function through the $args parameter. Hence we extract these
     * arguments *before* we have obtained any form-based input through
     * xarVarFetch().
     */
    extract($args);

    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('invalid',  'str:1:', $invalid,  '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('song_name',   'str:1:', $song_name,   $song_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('artist_name',     'str:1:', $artist_name,     $artist_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('album_name',     'str:1:', $album_name,     $album_name, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status',     'str:1:', $status,     $status, XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    if (empty($song_name) || !is_string($song_name)) {
        $invalid['song_name'] = 1;
        $song_name = '';
    }
    if (empty($artist_name) || !is_string($artist_name)) {
        $invalid['artist_name'] = 1;
        $artist_name = '';
    }
    if (empty($album_name) || !is_string($album_name)) {
        $invalid['album_name'] = 1;
        $album_name = '';
    }
    if (empty($status) || !in_array($status, array('submitted','active','archived'))) {
        $invalid['status'] = 1;
        $album_name = 'submitted';
    }
    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the admin_new form
         * call the admin_new function and return the template vars
         */
        return xarModFunc('mp3jukebox', 'admin', 'new',
                          array('song_name' => $song_name,
                                'artist_name' => $artist_name,
                                'album_name' => $album_name,
                                'status' => $status,
                                'invalid' => $invalid));
    }
    /* Confirm authorisation code. This checks that the form had a valid
     * authorisation code attached to it. If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */
    if (!xarSecConfirmAuthKey()) return;
    /* Notable by its absence there is no security check here. This is because
     * the security check is carried out within the API function and as such we
     * do not duplicate the work here
     * The API function is called. Note that the name of the API function and
     *the name of this function are identical, this helps a lot when
     * programming more complex modules. The arguments to the function are
     * passed in as their own arguments array
     */
    $songid = xarModAPIFunc('mp3jukebox',
                          'admin',
                          'create',
                          array('song_name' => $song_name,
                                'artist_name' => $artist_name,
                                'album_name' => $album_name,
                                'status' => $status
                          )
                      );
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted. Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!isset($songid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work.
     * 
     * For that, we determine the itemposition and make sure we start at the position of
     * that item. This is obviously a lot more complicated usually than in this simple
     * situation where sorting (by name here) /filtering/where clauses are more complex.
     * In real modules, this needs to be *a lot smarter* than the mp3jukebox given here.
     *
     * A slightly better way would be to set view order on the exid, and use the recordcount
     * as starting position, or order descending on exid and use 1 as starting number.
     * The actual functionality of your module will determin what is best.
     */
    $allItems = xarModApiFunc('mp3jukebox','user','getallsongs');
    $newItemPosition = 0;
    foreach($allItems as $pos => $info) { 
        if($info['songid'] == $songid) {  $newItemPosition = $pos; break; }
    }
    xarResponseRedirect(xarModURL('mp3jukebox', 'admin', 'view',array('startnum' => $newItemPosition)));
    
    /* Return true, in this case */
    return true;
}
?>
