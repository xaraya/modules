<?php
/**
 * MP3 Jukebox Module event handler
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com/
 *
 * @subpackage MP3 Jukebox Module
 * @link http://xaraya.com/index.php
 * @author MP3 Jukebox Module Development Team
 */

/**
 * MP3 Jukebox Module event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author the MP3 Jukebox module development team
 * @return bool
 */
function mp3jukebox_eventapi_OnServerRequest()
{
    /* Code that needs to be executed by this module on each
     * page view ( = ServerRequest ) goes here
     * This function can call any api function of this module
     */
    return true;
}
?>