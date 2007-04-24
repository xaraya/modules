<?php
/**
 * Displays standard Overview page
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
 * Overview function that displays the standard Overview page
 *
 * This function shows the overview template, currently admin-main.xd.
 * The template contains overview and help texts
 *
 * @author the MP3 Jukebox module development team
 * @return array xarTplModule with $data containing template data
 * @since 3 Sept 2005
 */
function mp3jukebox_admin_overview()
{
   /* Security Check */
    if (!xarSecurityCheck('AdminMP3Jukebox',0)) return;

    $data=array();

    /* if there is a separate overview function return data to it
     * else just call the main function that displays the overview
     */

    return xarTplModule('mp3jukebox', 'admin', 'main', $data,'main');
}

?>
