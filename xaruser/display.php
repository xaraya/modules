<?php
/**
 * Display an item
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
 * Display an item
 *
 * This is a standard function to provide detailed information on a single item
 * available from the module.
 *
 * @author the MP3 Jukebox module development team
 * @param  array $args an array of arguments (if called by other modules)
 * @param  int $args['objectid'] a generic object id (if called by other modules)
 * @param  int $args['playlistid'] the item id used for this mp3jukebox module
 * @return array $data The array that contains all data for the template
 */
function mp3jukebox_user_display($args)
{
    /* User functions of this type can be called by other modules. If this
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
    if (!xarVarFetch('playlistid', 'id', $playlistid)) return;
    if (!xarVarFetch('objectid', 'id', $objectid, $objectid, XARVAR_NOT_REQUIRED)) return;

    /* At this stage we check to see if we have been passed $objectid, the
     * generic item identifier. This could have been passed in by a hook or
     * through some other function calling this as part of a larger module, but
     * if it exists it overrides $playlistid

     * Note that this module could just use $objectid everywhere to avoid all
     * of this munging of variables, but then the resultant code is less
     * descriptive, especially where multiple objects are being used. The
     * decision of which of these ways to go is up to the module developer
     */
    if (!empty($objectid)) {
        $playlistid = $objectid;
    }
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = array();
    /* Prepare the variable that will hold some status message if necessary */
    $data['status'] = '';
    /* The API function is called. The arguments to the function are passed in
     * as their own arguments array.
     * Security check 1 - the get() function will fail if the user does not
     * have at least READ access to this item (also see below).
     */
    $item = xarModAPIFunc('mp3jukebox',
        'user',
        'get',
        array('playlistid' => $playlistid));
    if (!isset($item) && xarCurrentErrorType() != XAR_NO_EXCEPTION) return; /* throw back */

    /* If your module deals with different types of items, you should specify the item type
     * here, before calling any hooks
     * $item['itemtype'] = 0;
     * Security check 2 - if your API function does *not* check for the
     * appropriate access rights, or if for some reason you require higher
     * access than READ for this function, you *must* check this here !
     * if (!xarSecurityCheck('CommentMP3Jukebox',0,'Item',"$item[name]:All:$item[playlistid]")) {
     *    //Fill in the status variable with the status to be shown
     * $data['status'] = _EXAMPLENOAUTH;
     *    //Return the template variables defined in this function
     * return $data;
     *}
     */

    /* Let any transformation hooks know that we want to transform some text.
     * You'll need to specify the item id, and an array containing the names of all
     * the pieces of text that you want to transform (e.g. for autolinks, wiki,
     * smilies, bbcode, ...).
     */
    $item['transform'] = array('title');
    $item = xarModCallHooks('item',
        'transform',
        $playlistid,
        $item);
    /* Fill in the details of the item. Note that a module variable is used here to determine
     * whether or not parts of the item information should be displayed in
     * bold type or not
     */
    $data['playlistid'] = $playlistid;

    /* Note : module variables can also be specified directly in the
     * blocklayout template by using &xar-mod-<modname>-<varname>;
     * Note that you could also pass on the $item variable, and specify
     * the labels directly in the blocklayout template. But make sure you
     * use the <xar:ml>, <xar:mlstring> or <xar:mlkey> tags then, so that
     * labels can be translated for other languages...
     * Save the currently displayed item ID in a temporary variable cache
     * for any blocks that might be interested (e.g. the Others block)
     * You should use this -instead of globals- if you want to make
     * information available elsewhere in the processing of this page request
     */
    xarVarSetCached('Blocks.mp3jukebox', 'playlistid', $playlistid);
    /* Let any hooks know that we are displaying an item. As this is a display
     * hook we're passing a return URL in the item info, which is the URL that any
     * hooks will show after they have finished their own work. It is normal
     * for that URL to bring the user back to this function
     */
    $item['returnurl'] = xarModURL('mp3jukebox',
        'user',
        'display',
        array('playlistid' => $playlistid));
    $item['module'] = 'mp3jukebox';
    $hooks = xarModCallHooks('item',
        'display',
        $playlistid,
        $item);
    if (empty($hooks)) {
        $data['hookoutput'] = '';
    } else {
        /* You can use the output from individual hooks in your template too, e.g. with
         * $hookoutput['comments'], $hookoutput['hitcount'], $hookoutput['ratings'] etc.
         */
        $data['hookoutput'] = $hooks;
    }
    /* Once again, we are changing the name of the title for better
     * Search engine capability.
     */
    xarTplSetPageTitle(xarML('PLaylist') . ' ' . xarVarPrepForDisplay($item['title']));
    /* Return the template variables defined in this function */

    $data['songs'] = xarModAPIFunc('mp3jukebox','user','getallplaylistsongs', array('playlistid' => $playlistid));

    $data = array_merge($data, $item);

    $data['authid'] = xarSecGenAuthKey('mp3jukebox');

    return $data;
    /* Note : instead of using the $data variable, you could also specify
     * the different template variables directly in your return statement :
     *
     * return array('menu' => ...,
     * 'item' => ...,
     * 'hookoutput' => ...,
     * ... => ...);
     */
}
?>
