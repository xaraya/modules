<?php
/**
 * Twitter Module 
 *
 * @package modules
 * @copyright (C) 2002-2006 The Digital Development Foundation
 * @license GPL {@link http://www.gnu.org/licenses/gpl.html}
 * @link http://www.xaraya.com
 *
 * @subpackage Twitter Module
 * @link http://xaraya.com/index.php/release/991.html
 * @author Chris Powis (crisp@crispcreations.co.uk)
 */
/**
 * Extract function and arguments from short URLs for this module, and pass
 * them back to xarGetRequestInfo()
 *
 * @author Chris Powis (crisp@crispcreations.co.uk)
 * @param  array $params array containing the different elements of the virtual path
 * @return array containing func the function to be called and args the query
 *          string arguments, or empty if it failed
 */
function twitter_userapi_decode_shorturl($params)
{
    /* Initialise the argument list we will return */
    $args = array();
    $module = 'twitter';
    /* Check and see if we have a module alias */
    $aliasisset = xarModGetVar($module, 'useModuleAlias');
    $aliasname = xarModGetVar($module,'aliasname');
    if (($aliasisset) && isset($aliasname)) {
        $usealias   = true;
    } else{
        $usealias = false;
    }

    /* Analyse the different parts of the virtual path
     * $params[1] contains the first part after index.php/example
     * In general, you should be strict in encoding URLs, but as liberal
     * as possible in trying to decode them...
     */
    if ($params[0] != $module) { /* it's possibly some type of alias */
        $aliasname = xarModGetVar($module,'aliasname');
    }
    if (empty($params[1])) {
        /*( nothing specified -> we'll go to the main function */
        return array('main', $args);
    } elseif (preg_match('/^index/i', $params[1])) {
        return array('main', $args);
    } elseif (preg_match('/^tweet/i', $params[1])) {
        return array('tweet', $args);
    } elseif (preg_match('/^(\w+)/', $params[1], $matches)) {
        $timeline = $matches[1];
        $args['timeline'] = $timeline;
        return array('main', $args);
    } else {

    }
    /* default : return nothing -> no short URL decoded */
}
?>