<?php
/**
 * JpGraph Module event handler
 *
 * @package modules
 * @copyright (C) 2006-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */

/**
 * Module event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 * We try to have this function create the defines we want, but this interfers with general DEFINES
 *
 * @author the JpGraph module development team
 * @return bool
 */
function jpgraph_eventapi_OnServerRequest()
{
    //$defines = xarModAPIFunc('jpgraph','admin','defines');
    /* Code that needs to be executed by this module on each
     * page view ( = ServerRequest ) goes here
     * This function can call any api function of this module
     */
    return true;
}
?>