<?php
/**
 * Modify module's configuration
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
 * Modify module's configuration
 *
 * This is a standard function to modify the configuration parameters of the
 * module
 *
 * @author MP3 Jukebox module development team
 * @return array
 */
function mp3jukebox_admin_modifyconfig()
{ 
    /* Initialise the $data variable that will hold the data to be used in
     * the blocklayout template, and get the common menu configuration - it
     * helps if all of the module pages have a standard menu at the top to
     * support easy navigation
     */
    $data = array();

    /* common menu configuration */
    $data = xarModAPIFunc('mp3jukebox', 'admin', 'menu');
    
    /* Security check - important to do this as early as possible to avoid
     * potential security holes or just too much wasted processing
     */
    if (!xarSecurityCheck('AdminMP3Jukebox')) return;

    /* Generate a one-time authorisation code for this operation */
    $data['authid'] = xarSecGenAuthKey();

    /* Specify some values for display */
    $data['itemsvalue'] = xarModGetVar('mp3jukebox', 'itemsperpage');

    $data['playlistsvalue'] = xarModGetVar('mp3jukebox', 'playlistsperuser');
    $data['songsvalue'] = xarModGetVar('mp3jukebox', 'songsperplaylist');

    /* Note : if you don't plan on providing encode/decode functions for
     * short URLs (see xaruserapi.php), you should remove this from your
     * admin-modifyconfig.xd template.
     */
    $data['shorturlschecked'] = xarModGetVar('mp3jukebox', 'SupportShortURLs') ? true : false;

    /* If you plan to use alias names for you module then you can use the next two alias vars
     * You must also use short URLS for aliases, and provide appropriate encode/decode functions.
     */
    $data['useAliasName'] = xarModGetVar('mp3jukebox', 'useModuleAlias');
    $data['aliasname ']= xarModGetVar('mp3jukebox','aliasname');

    $hooks = xarModCallHooks('module', 'modifyconfig', 'mp3jukebox',
                       array('module' => 'mp3jukebox'));
    if (empty($hooks)) {
        $data['hooks'] = array('categories' => xarML('You can assign base categories by enabling the categories hooks for mp3jukebox module'));
    } else {
        $data['hooks'] = $hooks;
    
         /* You can use the output from individual hooks in your template too, e.g. with
         * $hooks['categories'], $hooks['dynamicdata'], $hooks['keywords'] etc.
         */
        $data['hookoutput'] = $hooks;
    }

    /* Return the template variables defined in this function */
    return $data;
}
?>
