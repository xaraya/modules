<?php
/**
 * Purpose of file
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
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
function weather_userapi_decode_shorturl(&$params) 
{    
    $args = array();
    
    // if we don't have a function, call the default view
    if(empty($params[1])) {
        return array('main', $args);
    } 
    
    if($params[1] == 'current') {
        $func = 'main';
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        }
    } elseif($params[1] == 'details') {
        $func = 'details';
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        }
    } elseif($params[1] == 'search') {
        $func = 'search';
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        } 
    } elseif($params[1] == 'modify') {
        $func = 'modifyconfig'; 
        if(isset($params[2])) {
            // this should be a location
            $args['xwloc'] = $params[2];
        } 
    } else {
            // this should be a location
            $func = 'main';
            $args['xwloc'] = $params[1];
    }
    
    if (empty($func)){
        $func = array();
    }

    // return the decoded information
    return array($func,$args);

}
?>