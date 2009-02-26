<?php
/**
 * Decode short urls
 *
 * @package Xaraya
 * @copyright (C) 2004-2007 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://xaraya.com
 *
 * @subpackage Xarigami SiteContact Module
 * @copyright (C) 2007,2008 2skies.com
 * @link http://xarigami.com/project/sitecontact
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 */

/**
 * extract function and arguments from short URLs and pass back to xarGetRequestInfo()
 *
 * @author Jo Dalle Nogare <icedlava@2skies.com>
 * @param  array $params containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function sitecontact_userapi_decode_shorturl($params)
{

    /* Initialise the argument list we will return */
    $args = array();
    $aliasisset = xarModGetVar('sitecontact', 'useModuleAlias');
    $aliasname = xarModGetVar('sitecontact','aliasname');

    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }
    $module = 'sitecontact';
    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar('sitecontact','aliasname');
    }
    if ((strtolower($params[0]) == 'sitecontact') || (strtolower($params[0] == $aliasname))) {
        array_shift($params);
    }
  
   $sctypes = xarModAPIFunc('sitecontact','user','getcontacttypes');

    /* If no path components then return. */
   if (empty($params[0])) {
        /* nothing specified -> we'll go to the main function */
        return array('main', $args);
        
    } elseif (preg_match('/^respond/i', $params[0])) {
        if (!empty($params[1]) && (preg_match('/^(\d+)/', $params[1], $matches))){
            $args['message'] = (int)$matches[0];
            if (!empty($params[2]) &&  (preg_match('/^(\d+)/', $params[2], $matches)))  {
              $args['scid'] = (int)$matches[0];
            }
        }elseif (!empty($params[1]) && empty($params[2]) && (preg_match('/^(\d+)/', $params[1], $matches))){
            $args['message'] = (int)$matches[0];
            $args['scid'] = null;
        }
        return array('respond', $args);
    } elseif (!empty($params[0]) && (preg_match('/^(\d+)/', $params[0], $matches)) && $matches[0] <=2) {
        $args['message'] = $matches[0];
        if (!empty($params[1])  &&  (preg_match('/^(\d+)/', $params[1], $matches))) {
            $args['scid'] = (int)$matches[0];
        }
         return array('main', $args);
    } elseif (!empty($params[0]) && (preg_match('/^(\w+)/',$params[0],$matches))) {
        $args['scform'] = $matches[0];
        return array('main', $args);

    } elseif (preg_match('/^index/i', $params[0])) {
        /* some search engine/someone tried using index.html (or similar)
         * -> we'll go to the main function
         */
        return array('main', $args);

    } elseif (preg_match('/^(\d+)/', $params[0], $matches)) {
         $message = $matches[0];
        $args['message'] = (int)$message;
        return array('main', $args);
    } else {

    }
}
?>