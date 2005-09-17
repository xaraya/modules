<?php
/**
 * Example event API functions
 *
 * @package Xaraya eXtensible Management System
 * @copyright (C) 2005 by the Xaraya Development Team.
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage example
 * @author Example module development team 
 */

/**
 * example event handler for the system event ServerRequest
 *
 * this function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author the Example module development team
 * @return bool
 */
function example_eventapi_OnServerRequest()
{
    // Code that needs to be executed by this module on each
    // page view ( = ServerRequest ) goes here
    // This function can call any api function of this module

    return true;
}
?>