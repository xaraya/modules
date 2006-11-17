<?php
/**
 * Set general PHP defines
 *
 * @package modules
 * @copyright (C) 2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage JpGraph Module
 * @link http://xaraya.com/index.php/release/819.html
 * @author JpGraph Module Development Team
 */
/**
 * JpGraph Module event handler for the system event ServerRequest
 *
 * This function is called when the system triggers the
 * event in index.php on each Server Request
 *
 * @author the JpGraph module development team
 * @return bool true
 */
function jpgraph_adminapi_defines()
{
    if(!defined('TTF_DIR')) {
        DEFINE('TTF_DIR', xarModGetVar('jpgraph','ttfdirectory'));
    }
    if(!defined('CACHE_DIR')) {
        DEFINE('CACHE_DIR',xarModGetVar('jpgraph','cachedirectory'));
    }
    if (!defined("USE_CACHE")) {
        $usecache = xarModGetVar('jpgraph','cachedirectory') ? true : false;
        DEFINE("USE_CACHE",$usecache);
    }

    /* Code that needs to be executed by this module on each
     * page view ( = ServerRequest ) goes here
     * This function can call any api function of this module
     */
    return true;
}
?>