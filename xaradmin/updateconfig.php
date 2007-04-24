<?php
/**
 * Standard function to update module configuration parameters
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
 * Standard function to update module configuration parameters
 *
 * This is a standard function to update the configuration parameters of the
 * module given the information passed back by the modification form
 * @author MP3 Jukebox module development team
 */
function mp3jukebox_admin_updateconfig()
{
    /* Get parameters from whatever input we need. All arguments to this
     * function should be obtained from xarVarFetch(). xarVarFetch allows
     * the checking of the input variables as well as setting default
     * values if needed. Getting vars from other places such as the
     * environment is not allowed, as that makes assumptions that will
     * not hold in future versions of Xaraya
     */
    if (!xarVarFetch('itemsperpage', 'int',      $itemsperpage, 10, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('playlistsperuser', 'int',      $playlistsperuser, 5, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('songsperplaylist', 'int',      $songsperplaylist, 100, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('shorturls',    'checkbox', $shorturls, false, XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('aliasname',    'str:1:',   $aliasname, '', XARVAR_NOT_REQUIRED)) return;
    if (!xarVarFetch('modulealias',  'checkbox', $modulealias,false,XARVAR_NOT_REQUIRED)) return;

    /* Confirm authorisation code. This checks that the form had a valid
     * authorisation code attached to it. If it did not then the function will
     * proceed no further as it is possible that this is an attempt at sending
     * in false data to the system
     */

    if (!xarSecConfirmAuthKey()) return;
    /* Update module variables. Note that the default values are set in
     * xarVarFetch when recieving the incoming values, so no extra processing
     * is needed when setting the variables here.
     */
    xarModSetVar('mp3jukebox', 'itemsperpage', $itemsperpage);
    xarModSetVar('mp3jukebox', 'playlistsperuser', $playlistsperuser);
    xarModSetVar('mp3jukebox', 'songsperplaylist', $songsperplaylist);
    xarModSetVar('mp3jukebox', 'SupportShortURLs', $shorturls);
    if (isset($aliasname) && trim($aliasname)<>'') {
        xarModSetVar('mp3jukebox', 'useModuleAlias', $modulealias);
    } else{
         xarModSetVar('mp3jukebox', 'useModuleAlias', 0);
    }
    $currentalias = xarModGetVar('mp3jukebox','aliasname');
    $newalias = trim($aliasname);
    /* Get rid of the spaces if any, it's easier here and use that as the alias*/
    if ( strpos($newalias,'_') === FALSE )
    {
        $newalias = str_replace(' ','_',$newalias);
    }
    $hasalias= xarModGetAlias($currentalias);
    $useAliasName= xarModGetVar('mp3jukebox','useModuleAlias');

    if (($useAliasName==1) && !empty($newalias)){
        /* we want to use an aliasname */
        /* First check for old alias and delete it */
        if (isset($hasalias) && ($hasalias =='mp3jukebox')){
            xarModDelAlias($currentalias,'mp3jukebox');
        }
        /* now set the new alias if it's a new one */
          xarModSetAlias($newalias,'mp3jukebox');
    }
    /* now set the alias modvar */
    xarModSetVar('mp3jukebox', 'aliasname', $newalias);

    xarModCallHooks('module','updateconfig','mp3jukebox',
                   array('module' => 'mp3jukebox'));

    /* This function generated no output, and so now it is complete we redirect
     * the user to an appropriate page for them to carry on their work
     */
    xarResponseRedirect(xarModURL('mp3jukebox', 'admin', 'modifyconfig'));

    /* Return */
    return true;
}
?>
