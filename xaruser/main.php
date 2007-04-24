<?php
/**
 * The main user function
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
 * The main user function
 *
 * This function is the default function, and is called whenever the module is
 * initiated without defining arguments. As such it can be used for a number
 * of things, but most commonly it either just shows the module menu and
 * returns or calls whatever the module designer feels should be the default
 * function (often this is the view() function)
 *
 * @author the MP3 Jukebox module development team
 * @return array $data An array with the data for the template
 */
function mp3jukebox_user_main()
{
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing. For the
     * main function we want to check that the user has at least overview
     * privilege for some item within this component, or else they won't be
     * able to see anything and so we refuse access altogether. The lowest
     * level of access for users depends on the particular module, but it is
     * generally either 'overview' or 'read'
     */
    if (!xarSecurityCheck('ViewMP3Jukebox')) return;
    /* If you want to go directly to some default function, instead of
     * having a separate main function, you can simply call it here, and
     * use the same template for user-main.xard as for user-view.xard
     * return xarModFunc('mp3jukebox','user','view');
     * Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = xarModAPIFunc('mp3jukebox', 'user', 'menu');
    /* Specify some other variables used in the blocklayout template */
    $data['welcome'] = xarML('Welcome to the MP3 Jukebox module...');
    /* We also may want to change the title of the page for a little
     * better search results from the spiders. All we are doing below
     * Is telling Xaraya what the title of the page should be, and
     * Xaraya controls the rest.
     */
    xarTplSetPageTitle(xarVarPrepForDisplay(xarML('Welcome')));
    /* Return the template variables defined in this function */
//    return $data;
    /* Note : instead of using the $data variable, you could also specify
     * the different template variables directly in your return statement :

     * return array('menutitle' => ...,
     * 'welcome' => ...,
     * ... => ...);
     */
     return xarModFunc('mp3jukebox', 'user', 'view');
}
?>
