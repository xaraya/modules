<?php
/**
 * Comments Module
 *
 * @package modules
 * @subpackage comments
 * @category Third Party Xaraya Module
 * @version 2.4.0
 * @copyright see the html/credits.html file in this release
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com/index.php/release/14.html
 * @author Marc Lutolf <mfl@netspan.ch>
 */
/**
 * Retrieves the host name of the commentor
 *
 */
function comments_userapi_get_hostname()
{
    $forwarded = xarServer::getVar('HTTP_X_FORWARDED_FOR');
    if (!empty($forwarded)) {
        $hostname = preg_replace('/,.*/', '', $forwarded);
    } else {
        $hostname = xarServer::getVar('REMOTE_ADDR');
    }
    return $hostname;
}
