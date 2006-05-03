<?php
/**
 * Example Module event handler
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Example Module
 * @link http://xaraya.com/index.php/release/36.html
 * @author Example Module Development Team
 */

/**
 * Example Module event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author the Example module development team
 * @return bool
 */
function example_eventapi_OnServerRequest()
{
    /* Code that needs to be executed by this module on each
     * page view ( = ServerRequest ) goes here
     * This function can call any api function of this module
     */
    return true;
}
?>