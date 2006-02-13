<?php
/**
 * Extract function and arguments from short URLs
 *
 * @package modules
 * @copyright (C) 2002-2005 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage legis Module
 * @link http://xaraya.com/index.php/release/593.html
 * @author jojodee
 */

/**
 * Extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 * 
 * @author jojodee
 * @param  $params array containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function legis_userapi_decode_shorturl($params)
{
    /* Initialise the argument list we will return */
    $args = array();
    $module = 'legis';
    /* Check and see if we have a module alias */
    $aliasisset = xarModGetVar('legis', 'useModuleAlias');
    $aliasname = xarModGetVar('legis','aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar('legis','aliasname');
    }
    if (empty($params[1])) {
        /*( nothing specified -> we'll go to the main function */
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {

        return array('main', $args);
  
    } elseif (preg_match('/^view/i', $params[1])) {
        /* something that starts with 'view' is probably for the view function of valid docs*/
       $args['docstatus']=2;
        return array('view', $args);
    } elseif (preg_match('/^pending/i', $params[1])) {
        /* something that starts with 'pending' is the view function for pending docs */
        $args['docstatus']=1;
        return array('view', $args);

    } elseif (preg_match('/^display/i', $params[1])) {
        if (preg_match('/^(\d+)/', $params[2], $matches)) {

        $cdid = (int)$matches[1];
        $args['cdid'] = $cdid;
        }
        return array('display', $args);
    } elseif (preg_match('/^sethall/i', $params[1])) {
        if (preg_match('/^(\d+)/', $params[2], $matches)) {

        $defaulthall = (int)$matches[1];
        $args['defaulthall'] = $defaulthall;
        }
        return array('main', $args);
    } elseif (preg_match('/^add/i', $params[1])){
         /* something that starts with 'add' is addlegis */
       return array('addlegis', $args);
    }
    /* default : return nothing -> no short URL decoded */
} 
?>