<?php
/**
 * Create a new item
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
 * Create a new item
 *
 * Standard function to create a new item
 * This is a standard function that is called with the results of the
 * form supplied by xarModFunc('mp3jukebox','user','new') to create a new item
 *
 * @author MP3 Jukebox module development team
 * @param  string $args['title']   the name of the item to be created
 * @param  bool    $args['private'] the number of the item to be created
 * @param  bool    $args['featured'] the number of the item to be created
 * @param  string  $args['status'] the number of the item to be created
 */
function mp3jukebox_user_create($args)
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
    if (!xarVarFetch('title',   'str:1:', $title,   $title, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('private',     'checkbox', $private,     $private, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('featured',     'checkbox', $featured,     $featured, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('status',     'str:1:', $status,     $status, XARVAR_NOT_REQUIRED)) return;

    // Argument check
    $invalid = array();
    if (empty($title) || !is_string($title)) {
        $invalid['title'] = 1;
        $title = '';
    }
    if ($private != 1) {
        $private = 0;
    }
    if ($featured != 1) {
        $featured = 0;
    }
    if (empty($status) || !in_array($status, array('submitted','active','archived'))) {
        $invalid['status'] = 1;
        $status = 'submitted';
    }
    // check if we have any errors
    if (count($invalid) > 0) {
        /* If we get here, we have encountered errors.
         * Send the user back to the user_new form
         * call the user_new function and return the template vars
         */
        return xarModFunc('mp3jukebox', 'user', 'new',
                          array('title' => $title,
                                'private' => $private,
                                'featured' => $featured,
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
    $playlistid = xarModAPIFunc('mp3jukebox',
                          'user',
                          'create',
                          array('title' => $title,
                                'private' => $private,
                                'featured' => $featured,
                                'status' => $status
                          )
                      );
    /* The return value of the function is checked here, and if the function
     * suceeded then an appropriate message is posted. Note that if the
     * function did not succeed then the API function should have already
     * posted a failure message so no action is required
     */
    if (!isset($playlistid) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; // throw back
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
    $allItems = xarModApiFunc('mp3jukebox','user','getallplaylists');
    $newItemPosition = 0;
    foreach($allItems as $pos => $info) { 
        if($info['playlistid'] == $playlistid) {  $newItemPosition = $pos; break; }
    }
    xarResponseRedirect(xarModURL('mp3jukebox', 'user', 'view',array('startnum' => $newItemPosition)));
    
    /* Return true, in this case */
    return true;
}
?>
