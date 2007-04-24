<?php
/**
 * Display song info
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
 * Display song info
 *
 * This is a standard function that is called whenever an administrator
 * wishes to display a current module item
 *
 * @author MP3 Jukebox Module Development Team
 * @param array  $args An array containing all the arguments to this function.
 * @param int    $songid The id of the item to be modified
 * @param int    $objectid The id of the unified object, for use with other modules
 * @return array $item containing all elements and variables for the template
 */
function mp3jukebox_admin_displaysong($args)
{

    /* Admin functions of this type can be called by other modules. If this
     * happens then the calling module will be able to pass in arguments to
     * this function through the $args parameter. Hence we extract these
     * arguments *before* we have obtained any form-based input through
     * xarVarFetch(), so that parameters passed by the modules can also be
     * checked by a certain validation.
     */
    extract($args);

    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('songid',     'int:1:',     $songid)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier. This could have been passed in by a hook or
     * through some other function calling this as part of a larger module, but
     * if it exists it overrides $exid
     *
     * Note that this module could just use $objectid everywhere to avoid all
     * of this munging of variables, but then the resultant code is less
     * descriptive, especially where multiple objects are being used. The
     * decision of which of these ways to go is up to the module developer
     */
    if (!empty($objectid)) {
        $songid = $objectid;
    }
    /* The user API function is called. This takes the item ID which we
     * obtained from the input and gets us the information on the appropriate
     * item. If the item does not exist we post an appropriate message and
     * return
     */
    $item = xarModAPIFunc('mp3jukebox',
                          'user',
                          'getsong',
                          array('songid' => $songid));

    /* Check for exceptions */
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing. However,
     * in this case we had to wait until we could obtain the item name to
     * complete the instance information so this is the first chance we get to
     * do the check
     */
    if (!xarSecurityCheck('ViewMP3Jukebox', 1, 'Song', "$item[song_name]:All:$songid")) {
        return;
    }
    /* Get menu variables - it helps if all of the module pages have a standard
     * menu at their head to aid in navigation. The mp3jukebox here includes a
     * menu for this function, hence the specification of 'modify'
     * $menu = xarModAPIFunc('mp3jukebox','admin','menu','modify');
     */
    /* Call the hooks
     * This mp3jukebox module doesn't use itemtypes
     * We will therefor pass NULL as an itemtype. When you define itemtypes, you should 
     * pass it to the call for the hooks here
     */
    $item['module'] = 'mp3jukebox';
    $item['itemtype'] = 'Song';
    $hooks = xarModCallHooks('item', 'modify', $songid, $item);

    /* Return the template variables defined in this function */
    return $item;
}
?>

