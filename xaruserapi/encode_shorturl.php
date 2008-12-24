<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) copyright-placeholder
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Weather Module
 * @link http://xaraya.com/index.php/release/662.html
 * @author Weather Module Development Team
 */

/**
 * @author Roger Raymond
 */
function weather_userapi_encode_shorturl(&$params) 
{
    // Get arguments from argument array
    if (!isset($params['func'])) { return; }
    
    // default path is empty -> no short URL
    $path = '';
    $extra = '';
    
    // we can't rely on xarModGetName() here (yet) !
    $module = 'weather';
    
    // specify some short URLs relevant to your module
    switch($params['func']) {
        case 'main':
        case 'cc':
            $path = "/$module/";
            break;
        
        case 'details':
            $path = "/$module/details/";
            break;
        
        case 'search':
            $path = "/$module/search/";
            break;
            
        case 'modifyconfig':
            $path = "/$module/modify/";
            break;
    }
    if(isset($params['xwloc'])) {
        $path .= $params['xwloc'].'/';
    }
    if(!empty($path) && isset($params['xwunits'])) {
        $join = empty($extra) ? '?' : '&';
        $extra .= $join . 'xwunits=' . $params['xwunits'];
    }
    if(!empty($path) && isset($params['xwday'])) {
        $join = empty($extra) ? '?' : '&';
        $extra .= $join . 'xwday=' . $params['xwday'];
    }
    return $path.$extra;
}

?>